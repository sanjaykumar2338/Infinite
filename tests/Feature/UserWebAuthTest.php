<?php

namespace Tests\Feature;

use App\Models\BadgeReport;
use App\Models\Report;
use App\Models\ReportChart;
use App\Models\User;
use App\Services\FirebaseAuthService;
use App\Services\StripeBillingService;
use App\Support\ForgeSundayWeeklyBrief;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\Checkout\Session;
use Stripe\Exception\InvalidRequestException;
use Tests\TestCase;

class UserWebAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_redirects_guest_to_login(): void
    {
        $this->get('/dashboard')
            ->assertRedirect('/login');
    }

    public function test_firebase_session_login_creates_user_and_redirects_to_dashboard(): void
    {
        $this->fakeFirebase([
            'uid' => 'web-firebase-uid',
            'email' => 'web@example.com',
            'name' => 'Web User',
        ]);

        $this->postJson('/login/firebase', ['id_token' => 'valid-token'])
            ->assertOk()
            ->assertJsonPath('redirect', route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'firebase_uid' => 'web-firebase-uid',
            'email' => 'web@example.com',
            'plan' => 'free',
            'status' => 'free',
        ]);
    }

    public function test_user_dashboard_shows_user_access_and_reports(): void
    {
        $user = User::factory()->create([
            'name' => 'Forge Customer',
            'email' => 'forge-customer@example.com',
            'plan' => 'forge',
            'status' => 'active',
            'free_call_used' => true,
            'call_minutes_used' => 42,
        ]);

        Report::create([
            'user_id' => $user->id,
            'title' => 'Weekly Coaching Report',
            'summary' => 'Progress and next steps',
            'published_at' => now(),
        ]);

        ReportChart::create([
            'user_id' => $user->id,
            'title' => 'Engagement KPI Chart',
            'chart_type' => 'weekly',
            'published_at' => now(),
        ]);

        BadgeReport::create([
            'user_id' => $user->id,
            'title' => 'Monthly Confidence Badge',
            'badge_name' => 'Confidence Builder',
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Forge Customer')
            ->assertSee('Forge')
            ->assertSee('Active')
            ->assertSee('42')
            ->assertSee('Unlocked')
            ->assertSee('Weekly Coaching Report')
            ->assertSee('Engagement KPI Chart')
            ->assertSee('Monthly Confidence Badge')
            ->assertSee('Install Extension')
            ->assertSee('Logout');
    }

    public function test_free_user_cannot_access_forge_sunday_report(): void
    {
        $user = User::factory()->create([
            'plan' => 'free',
            'status' => 'free',
        ]);

        $report = Report::create([
            'user_id' => $user->id,
            'title' => 'FORGE — Weekly Brief',
            'report_type' => ForgeSundayWeeklyBrief::REPORT_TYPE,
            'report_data' => ForgeSundayWeeklyBrief::sample(),
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.reports.show', $report))
            ->assertForbidden();
    }

    public function test_spark_user_cannot_access_forge_sunday_report(): void
    {
        $user = User::factory()->create([
            'plan' => 'spark',
            'status' => 'active',
        ]);

        $report = Report::create([
            'user_id' => $user->id,
            'title' => 'FORGE — Weekly Brief',
            'report_type' => ForgeSundayWeeklyBrief::REPORT_TYPE,
            'report_data' => ForgeSundayWeeklyBrief::sample(),
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.reports.show', $report))
            ->assertForbidden();
    }

    public function test_forge_user_can_view_forge_sunday_report(): void
    {
        $user = User::factory()->create([
            'plan' => 'forge',
            'status' => 'active',
        ]);

        $payload = ForgeSundayWeeklyBrief::sample();

        $report = Report::create([
            'user_id' => $user->id,
            'title' => 'FORGE — Weekly Brief',
            'report_type' => ForgeSundayWeeklyBrief::REPORT_TYPE,
            'report_data' => $payload,
            'period_end' => now()->endOfWeek()->toDateString(),
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.reports.show', $report))
            ->assertOk()
            ->assertSee('FORGE — Weekly Brief')
            ->assertSee($payload['meta']['prepared_time'])
            ->assertSee($payload['executive_verdict']['headline'])
            ->assertSee($payload['business_translation_layer']['objection_handling'])
            ->assertSee($payload['next_week_focus']['text']);
    }

    public function test_forge_sunday_report_missing_fields_fails_safely(): void
    {
        $user = User::factory()->create([
            'plan' => 'forge',
            'status' => 'active',
        ]);

        $report = Report::create([
            'user_id' => $user->id,
            'title' => 'FORGE — Weekly Brief',
            'report_type' => ForgeSundayWeeklyBrief::REPORT_TYPE,
            'report_data' => [
                'meta' => [
                    'prepared_time' => 'Sunday 9:00 PM',
                ],
            ],
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.reports.show', $report))
            ->assertOk()
            ->assertSee('Forge Sunday report unavailable')
            ->assertSee('meta.system');
    }

    public function test_signed_in_home_install_button_downloads_extension(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertSee(route('extension.download'), false);
    }

    public function test_extension_download_requires_login(): void
    {
        $this->get('/extension/download')
            ->assertRedirect('/login');
    }

    public function test_signed_in_user_can_download_extension_archive(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/extension/download')
            ->assertOk()
            ->assertDownload('infinite-sugar-extension.zip');
    }

    public function test_logged_in_pricing_checkout_redirects_to_stripe(): void
    {
        $user = User::factory()->create();
        $this->fakeStripeCheckout();

        $this->actingAs($user)
            ->get('/billing/checkout/forge')
            ->assertRedirect('https://checkout.stripe.test/session');
    }

    public function test_logged_in_pricing_checkout_shows_message_when_stripe_fails(): void
    {
        $user = User::factory()->create();
        $this->fakeStripeCheckoutFailure();

        $this->actingAs($user)
            ->get('/billing/checkout/spark')
            ->assertRedirect(route('pricing'))
            ->assertSessionHas('error', 'Unable to start Stripe Checkout right now. Please verify the billing price configuration or try again shortly.');

        $this->followingRedirects()
            ->actingAs($user)
            ->get('/billing/checkout/spark')
            ->assertOk()
            ->assertSee('Unable to start Stripe Checkout right now.');
    }

    public function test_guest_checkout_resumes_after_firebase_login(): void
    {
        $this->get('/billing/checkout/spark')
            ->assertRedirect('/login')
            ->assertSessionHas('checkout_plan', 'spark');

        $this->fakeFirebase([
            'uid' => 'resume-checkout-uid',
            'email' => 'resume@example.com',
            'name' => 'Resume Checkout',
        ]);

        $this->postJson('/login/firebase', ['id_token' => 'valid-token'])
            ->assertOk()
            ->assertJsonPath('redirect', route('billing.checkout', 'spark'));
    }

    public function test_user_logout_ends_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
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
}
