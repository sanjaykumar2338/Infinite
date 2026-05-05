<?php

namespace Tests\Feature;

use App\Models\CallSession;
use App\Models\StripeEvent;
use App\Models\User;
use App\Services\AccessService;
use App\Services\FirebaseAuthService;
use App\Services\StripeBillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\Checkout\Session;
use Stripe\Event;
use Stripe\Exception\InvalidRequestException;
use Tests\TestCase;

class SaasApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_firebase_authenticated_user_is_created(): void
    {
        $this->fakeFirebase([
            'uid' => 'firebase-123',
            'email' => 'new@example.com',
            'name' => 'New User',
        ]);

        $this->getJson('/api/me', $this->authHeaders())
            ->assertOk()
            ->assertJsonPath('user.email', 'new@example.com')
            ->assertJsonPath('user.plan', 'free');

        $this->assertDatabaseHas('users', [
            'firebase_uid' => 'firebase-123',
            'email' => 'new@example.com',
            'status' => 'free',
        ]);
    }

    public function test_access_check_for_free_spark_and_forge(): void
    {
        $free = User::factory()->create(['firebase_uid' => 'free-uid', 'plan' => 'free', 'status' => 'free']);
        $spark = User::factory()->create(['firebase_uid' => 'spark-uid', 'plan' => 'spark', 'status' => 'active']);
        $forge = User::factory()->create(['firebase_uid' => 'forge-uid', 'plan' => 'forge', 'status' => 'active']);

        $this->fakeFirebase(['uid' => $free->firebase_uid, 'email' => $free->email]);
        $this->getJson('/api/access/check', $this->authHeaders())
            ->assertOk()
            ->assertJsonPath('can_use_spark_call', true)
            ->assertJsonPath('can_use_reports', false)
            ->assertJsonPath('remaining_minutes', 30);

        $this->fakeFirebase(['uid' => $spark->firebase_uid, 'email' => $spark->email]);
        $this->getJson('/api/access/check', $this->authHeaders())
            ->assertOk()
            ->assertJsonPath('can_use_spark_call', true)
            ->assertJsonPath('can_use_live_insights', true)
            ->assertJsonPath('can_use_reports', false)
            ->assertJsonPath('remaining_minutes', null);

        $this->fakeFirebase(['uid' => $forge->firebase_uid, 'email' => $forge->email]);
        $this->getJson('/api/access/check', $this->authHeaders())
            ->assertOk()
            ->assertJsonPath('can_use_live_insights', true)
            ->assertJsonPath('can_use_reports', true);
    }

    public function test_free_spark_trial_stops_at_thirty_minutes(): void
    {
        $user = User::factory()->create([
            'firebase_uid' => 'trial-uid',
            'email' => 'trial@example.com',
            'call_minutes_used' => 29,
        ]);

        $this->fakeFirebase(['uid' => $user->firebase_uid, 'email' => $user->email]);

        $this->postJson('/api/call/start', [], $this->authHeaders())->assertOk();

        CallSession::first()->update(['started_at' => now()->subMinutes(2)]);

        $this->postJson('/api/call/usage', [], $this->authHeaders())
            ->assertOk()
            ->assertJsonPath('free_call_used', true)
            ->assertJsonPath('call_minutes_used', 30)
            ->assertJsonPath('remaining_minutes', 0);

        $this->postJson('/api/call/start', [], $this->authHeaders())
            ->assertStatus(402);
    }

    public function test_stripe_checkout_session_creation(): void
    {
        $user = User::factory()->create(['firebase_uid' => 'checkout-uid']);
        $this->fakeFirebase(['uid' => $user->firebase_uid, 'email' => $user->email]);
        $this->fakeStripeCheckout();

        $this->postJson('/api/billing/checkout/spark', [], $this->authHeaders())
            ->assertOk()
            ->assertJsonPath('checkout_session_id', 'cs_test_123')
            ->assertJsonPath('url', 'https://checkout.stripe.test/session');
    }

    public function test_stripe_checkout_api_returns_message_when_stripe_fails(): void
    {
        $user = User::factory()->create(['firebase_uid' => 'checkout-failure-uid']);
        $this->fakeFirebase(['uid' => $user->firebase_uid, 'email' => $user->email]);
        $this->fakeStripeCheckoutFailure();

        $this->postJson('/api/billing/checkout/spark', [], $this->authHeaders())
            ->assertStatus(502)
            ->assertJsonPath('message', 'Unable to start Stripe Checkout right now. Please verify the billing price configuration or try again shortly.');
    }

    public function test_stripe_webhook_idempotency(): void
    {
        $user = User::factory()->create(['email' => 'stripe@example.com']);
        $this->fakeStripeWebhook($this->eventPayload('evt_once', 'checkout.session.completed', [
            'customer' => 'cus_123',
            'subscription' => 'sub_123',
            'customer_email' => $user->email,
            'client_reference_id' => (string) $user->id,
            'metadata' => ['user_id' => (string) $user->id, 'plan' => 'forge'],
        ]));

        $payload = ['ignored' => true];

        $this->postJson('/api/stripe/webhook', $payload)->assertOk()->assertJsonPath('status', 'processed');
        $this->postJson('/api/stripe/webhook', $payload)->assertOk()->assertJsonPath('status', 'duplicate');

        $this->assertSame(1, StripeEvent::count());
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'plan' => 'forge',
            'status' => 'active',
            'stripe_customer_id' => 'cus_123',
        ]);
    }

    public function test_payment_succeeded_updates_user_active(): void
    {
        $user = User::factory()->create([
            'status' => 'past_due',
            'stripe_customer_id' => 'cus_paid',
            'stripe_subscription_id' => 'sub_paid',
        ]);

        $this->fakeStripeWebhook($this->eventPayload('evt_paid', 'invoice.payment_succeeded', [
            'customer' => 'cus_paid',
            'subscription' => 'sub_paid',
            'lines' => ['data' => [['period' => ['end' => now()->addMonth()->timestamp]]]],
        ]));

        $this->postJson('/api/stripe/webhook', [])->assertOk();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'active']);
        $this->assertNotNull($user->fresh()->current_period_end);
    }

    public function test_payment_failed_marks_past_due(): void
    {
        $periodEnd = now()->addWeek();
        $user = User::factory()->create([
            'plan' => 'spark',
            'status' => 'active',
            'stripe_customer_id' => 'cus_failed',
            'current_period_end' => $periodEnd,
        ]);
        $this->fakeStripeWebhook($this->eventPayload('evt_failed', 'invoice.payment_failed', [
            'customer' => 'cus_failed',
        ]));

        $this->postJson('/api/stripe/webhook', [])->assertOk();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'past_due']);
        $this->assertTrue(app(AccessService::class)->check($user->fresh())['can_use_spark_call']);
    }

    public function test_subscription_deleted_keeps_access_until_period_end(): void
    {
        $user = User::factory()->create([
            'plan' => 'forge',
            'status' => 'active',
            'stripe_subscription_id' => 'sub_cancel',
        ]);

        $this->fakeStripeWebhook($this->eventPayload('evt_cancel', 'customer.subscription.deleted', [
            'id' => 'sub_cancel',
            'current_period_end' => now()->addWeek()->timestamp,
        ]));

        $this->postJson('/api/stripe/webhook', [])->assertOk();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'plan' => 'forge', 'status' => 'cancelled']);
        $this->assertTrue(app(AccessService::class)->check($user->fresh())['can_use_reports']);
    }

    public function test_subscription_created_maps_price_to_plan(): void
    {
        config(['services.stripe.prices.spark' => 'price_spark']);
        $user = User::factory()->create(['plan' => 'free', 'status' => 'free']);

        $this->fakeStripeWebhook($this->eventPayload('evt_sub_created', 'customer.subscription.created', [
            'id' => 'sub_created',
            'customer' => 'cus_created',
            'status' => 'active',
            'current_period_end' => now()->addMonth()->timestamp,
            'metadata' => ['user_id' => (string) $user->id],
            'items' => ['data' => [['price' => ['id' => 'price_spark']]]],
        ]));

        $this->postJson('/api/stripe/webhook', [])->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'plan' => 'spark',
            'status' => 'active',
            'stripe_customer_id' => 'cus_created',
            'stripe_subscription_id' => 'sub_created',
        ]);
    }

    public function test_admin_tester_gets_full_access(): void
    {
        $tester = User::factory()->create(['firebase_uid' => 'tester-uid', 'role' => 'tester']);
        $this->fakeFirebase(['uid' => $tester->firebase_uid, 'email' => $tester->email]);

        $this->getJson('/api/access/check', $this->authHeaders())
            ->assertOk()
            ->assertJsonPath('can_use_live_insights', true)
            ->assertJsonPath('can_use_reports', true)
            ->assertJsonPath('remaining_minutes', null);
    }

    public function test_analyze_requires_firebase_authentication(): void
    {
        $this->postJson('/api/analyze', $this->analyzePayload())
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Missing bearer token.');
    }

    public function test_analyze_allows_limited_free_trial_access(): void
    {
        $free = User::factory()->create([
            'firebase_uid' => 'free-analyze-uid',
            'plan' => 'free',
            'status' => 'free',
            'call_minutes_used' => 0,
        ]);
        $this->fakeFirebase(['uid' => $free->firebase_uid, 'email' => $free->email]);

        $this->postJson('/api/analyze', $this->analyzePayload(), $this->authHeaders())
            ->assertOk()
            ->assertJsonPath('type', 'mock')
            ->assertJsonPath('signal', 'rapport')
            ->assertJsonPath('confidence', 0.72)
            ->assertJsonPath('features.live_guidance', true)
            ->assertJsonPath('features.advanced_reports', false)
            ->assertJsonStructure([
                'suggestion',
                'timestamp',
                'messages' => ['red', 'green', 'yellow', 'blue'],
                'nose_position',
                'access',
            ]);
    }

    public function test_analyze_blocks_free_users_after_trial_limit(): void
    {
        $free = User::factory()->create([
            'firebase_uid' => 'free-blocked-analyze-uid',
            'plan' => 'free',
            'status' => 'free',
            'call_minutes_used' => AccessService::FREE_TRIAL_MINUTES,
            'free_call_used' => true,
        ]);
        $this->fakeFirebase(['uid' => $free->firebase_uid, 'email' => $free->email]);

        $this->postJson('/api/analyze', $this->analyzePayload(), $this->authHeaders())
            ->assertForbidden()
            ->assertJsonPath('message', 'Upgrade required for live guidance.')
            ->assertJsonPath('access.remaining_minutes', 0);
    }

    public function test_analyze_allows_spark_users(): void
    {
        $spark = User::factory()->create([
            'firebase_uid' => 'spark-analyze-uid',
            'plan' => 'spark',
            'status' => 'active',
        ]);
        $this->fakeFirebase(['uid' => $spark->firebase_uid, 'email' => $spark->email]);

        $this->postJson('/api/analyze', $this->analyzePayload(['mode' => 2]), $this->authHeaders())
            ->assertOk()
            ->assertJsonPath('signal', 'attention')
            ->assertJsonPath('features.live_guidance', true)
            ->assertJsonPath('features.advanced_reports', false);
    }

    public function test_analyze_allows_forge_users_with_advanced_features(): void
    {
        $forge = User::factory()->create([
            'firebase_uid' => 'forge-analyze-uid',
            'plan' => 'forge',
            'status' => 'active',
        ]);
        $this->fakeFirebase(['uid' => $forge->firebase_uid, 'email' => $forge->email]);

        $this->postJson('/api/analyze', $this->analyzePayload(), $this->authHeaders())
            ->assertOk()
            ->assertJsonPath('features.live_guidance', true)
            ->assertJsonPath('features.advanced_reports', true)
            ->assertJsonPath('access.can_use_reports', true);
    }

    public function test_analyze_validates_extension_payload(): void
    {
        $spark = User::factory()->create([
            'firebase_uid' => 'invalid-analyze-uid',
            'plan' => 'spark',
            'status' => 'active',
        ]);
        $this->fakeFirebase(['uid' => $spark->firebase_uid, 'email' => $spark->email]);

        $this->postJson('/api/analyze', ['image' => 'not-an-image'], $this->authHeaders())
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['image']);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => 'Bearer firebase-token'];
    }

    private function analyzePayload(array $overrides = []): array
    {
        return array_merge([
            'image' => 'data:image/png;base64,'.base64_encode('fake image'),
            'mode' => 1,
        ], $overrides);
    }

    private function fakeFirebase(array $claims): void
    {
        $this->app->instance(FirebaseAuthService::class, new class($claims) extends FirebaseAuthService
        {
            public function __construct(private array $claims) {}

            public function verifyIdToken(string $token): array
            {
                return array_merge([
                    'uid' => 'uid',
                    'email' => 'user@example.com',
                    'name' => 'User',
                    'email_verified' => true,
                ], $this->claims);
            }
        });
    }

    private function fakeStripeCheckout(): void
    {
        $this->app->instance(StripeBillingService::class, new class extends StripeBillingService
        {
            public function createCheckoutSession(User $user, string $plan): Session
            {
                return Session::constructFrom([
                    'id' => 'cs_test_123',
                    'url' => 'https://checkout.stripe.test/session',
                ]);
            }
        });
    }

    private function fakeStripeCheckoutFailure(): void
    {
        $this->app->instance(StripeBillingService::class, new class extends StripeBillingService
        {
            public function createCheckoutSession(User $user, string $plan): Session
            {
                throw InvalidRequestException::factory("No such price: 'prod_bad'", 400);
            }
        });
    }

    private function fakeStripeWebhook(array $event): void
    {
        $this->app->instance(StripeBillingService::class, new class($event) extends StripeBillingService
        {
            public function __construct(private array $event) {}

            public function constructWebhookEvent(string $payload, ?string $signature): Event
            {
                return Event::constructFrom($this->event);
            }
        });
    }

    private function eventPayload(string $id, string $type, array $object): array
    {
        return [
            'id' => $id,
            'object' => 'event',
            'type' => $type,
            'data' => ['object' => $object],
        ];
    }
}
