<?php

namespace Tests\Feature;

use App\Models\CallSession;
use App\Models\StripeEvent;
use App\Models\User;
use App\Services\FirebaseAuthService;
use App\Services\StripeBillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\Checkout\Session;
use Stripe\Event;
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
        $user = User::factory()->create(['status' => 'active', 'stripe_customer_id' => 'cus_failed']);
        $this->fakeStripeWebhook($this->eventPayload('evt_failed', 'invoice.payment_failed', [
            'customer' => 'cus_failed',
        ]));

        $this->postJson('/api/stripe/webhook', [])->assertOk();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'past_due']);
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

    private function authHeaders(): array
    {
        return ['Authorization' => 'Bearer firebase-token'];
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
