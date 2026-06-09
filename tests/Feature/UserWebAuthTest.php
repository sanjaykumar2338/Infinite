<?php

namespace Tests\Feature;

use App\Models\BadgeReport;
use App\Models\PageContent;
use App\Models\Report;
use App\Models\ReportChart;
use App\Models\User;
use App\Services\FirebaseAuthService;
use App\Services\StripeBillingService;
use App\Support\ForgeMonthlyBadgeReport;
use App\Support\ForgeSundayWeeklyBrief;
use App\Support\ForgeWeeklyHeatmap;
use App\Support\ForgeWeeklyTimeline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

    public function test_dashboard_shows_spark_call_usage_and_remaining_minutes(): void
    {
        $user = User::factory()->create([
            'name' => 'Spark Trial',
            'email' => 'spark-trial@example.com',
            'plan' => 'free',
            'status' => 'free',
            'call_minutes_used' => 11,
            'free_call_used' => false,
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Free')
            ->assertSee('Free call minutes used')
            ->assertSee('11 / 30')
            ->assertSee('Remaining free minutes')
            ->assertSee('19')
            ->assertSee('Spark call')
            ->assertSee('Allowed');
    }

    public function test_pricing_page_shows_spark_free_live_call_copy(): void
    {
        $this->get('/pricing')
            ->assertOk()
            ->assertSee('Includes 1 free live call · 30 minutes', false);
    }

    public function test_homepage_displays_approved_sunday_and_monday_brief_cards(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('WEEKLY PERFORMANCE BRIEF • SUNDAY 9 PM', false)
            ->assertSee('Weekly Intelligence Brief')
            ->assertSee('Meaningful patterns surfaced')
            ->assertSee('Strategic observations delivered')
            ->assertSee('The week, distilled.')
            ->assertSee('MONTHLY PERFORMANCE SUMMARY • MONDAY 8 AM', false)
            ->assertSee('Achievement Review')
            ->assertSee('One earned badge')
            ->assertSee('Growth documented month after month')
            ->assertSee('No fluff. Just proof the edge is repeatable.');
    }

    public function test_dashboard_shows_forge_structured_report_links(): void
    {
        $user = User::factory()->create([
            'plan' => 'forge',
            'status' => 'active',
        ]);

        $sunday = Report::create([
            'user_id' => $user->id,
            'title' => 'FORGE — Weekly Brief',
            'report_type' => ForgeSundayWeeklyBrief::REPORT_TYPE,
            'report_data' => ForgeSundayWeeklyBrief::sample(),
            'published_at' => now(),
        ]);

        $timeline = ReportChart::create([
            'user_id' => $user->id,
            'title' => 'FORGE Weekly Strategic Timeline',
            'report_type' => ForgeWeeklyTimeline::REPORT_TYPE,
            'chart_type' => 'timeline',
            'data' => ForgeWeeklyTimeline::sample(),
            'published_at' => now(),
        ]);

        $heatmap = ReportChart::create([
            'user_id' => $user->id,
            'title' => 'FORGE Weekly Heatmap',
            'report_type' => ForgeWeeklyHeatmap::REPORT_TYPE,
            'chart_type' => 'heatmap',
            'data' => ForgeWeeklyHeatmap::sample(),
            'published_at' => now(),
        ]);

        $badge = BadgeReport::create([
            'user_id' => $user->id,
            'title' => ForgeMonthlyBadgeReport::BADGE_MOMENTUM_ARCHITECT,
            'report_type' => ForgeMonthlyBadgeReport::REPORT_TYPE,
            'badge_name' => ForgeMonthlyBadgeReport::BADGE_MOMENTUM_ARCHITECT,
            'report_data' => ForgeMonthlyBadgeReport::sample(ForgeMonthlyBadgeReport::BADGE_MOMENTUM_ARCHITECT),
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee(route('dashboard.reports.show', $sunday), false)
            ->assertSee(route('dashboard.charts.show', $timeline), false)
            ->assertSee(route('dashboard.charts.show', $heatmap), false)
            ->assertSee(route('dashboard.badges.show', $badge), false);
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
            ->assertSee('Sunday Night Executive Report')
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

    public function test_free_user_cannot_access_forge_timeline(): void
    {
        $user = User::factory()->create([
            'plan' => 'free',
            'status' => 'free',
        ]);

        $chart = ReportChart::create([
            'user_id' => $user->id,
            'title' => 'FORGE Weekly Strategic Timeline',
            'report_type' => ForgeWeeklyTimeline::REPORT_TYPE,
            'chart_type' => 'timeline',
            'data' => ForgeWeeklyTimeline::sample(),
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.charts.show', $chart))
            ->assertForbidden();
    }

    public function test_spark_user_cannot_access_forge_timeline(): void
    {
        $user = User::factory()->create([
            'plan' => 'spark',
            'status' => 'active',
        ]);

        $chart = ReportChart::create([
            'user_id' => $user->id,
            'title' => 'FORGE Weekly Strategic Timeline',
            'report_type' => ForgeWeeklyTimeline::REPORT_TYPE,
            'chart_type' => 'timeline',
            'data' => ForgeWeeklyTimeline::sample(),
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.charts.show', $chart))
            ->assertForbidden();
    }

    public function test_forge_user_can_access_forge_timeline(): void
    {
        $user = User::factory()->create([
            'plan' => 'forge',
            'status' => 'active',
        ]);

        $payload = ForgeWeeklyTimeline::sample();

        $chart = ReportChart::create([
            'user_id' => $user->id,
            'title' => 'FORGE Weekly Strategic Timeline',
            'report_type' => ForgeWeeklyTimeline::REPORT_TYPE,
            'chart_type' => 'timeline',
            'data' => $payload,
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.charts.show', $chart))
            ->assertOk()
            ->assertSee('FORGE Presence Intelligence')
            ->assertSee($payload['executive_summary']['headline'])
            ->assertSee($payload['directional_takeaway']['text']);
    }

    public function test_forge_timeline_missing_fields_fails_safely(): void
    {
        $user = User::factory()->create([
            'plan' => 'forge',
            'status' => 'active',
        ]);

        $chart = ReportChart::create([
            'user_id' => $user->id,
            'title' => 'FORGE Weekly Strategic Timeline',
            'report_type' => ForgeWeeklyTimeline::REPORT_TYPE,
            'chart_type' => 'timeline',
            'data' => [
                'report_type' => ForgeWeeklyTimeline::REPORT_TYPE,
                'meta' => ['prepared_time' => 'Sunday 9:00 PM'],
            ],
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.charts.show', $chart))
            ->assertOk()
            ->assertSee('Forge weekly timeline unavailable')
            ->assertSee('meta.system');
    }

    public function test_forge_user_can_access_forge_heatmap(): void
    {
        $user = User::factory()->create([
            'plan' => 'forge',
            'status' => 'active',
        ]);

        $payload = ForgeWeeklyHeatmap::sample();

        $chart = ReportChart::create([
            'user_id' => $user->id,
            'title' => 'FORGE Weekly Heatmap',
            'report_type' => ForgeWeeklyHeatmap::REPORT_TYPE,
            'chart_type' => 'heatmap',
            'data' => $payload,
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.charts.show', $chart))
            ->assertOk()
            ->assertSee('Weekly Heatmap')
            ->assertSee($payload['executive_summary']['headline'])
            ->assertSee($payload['strategic_interpretation']['text']);
    }

    public function test_spark_user_cannot_access_forge_heatmap(): void
    {
        $user = User::factory()->create([
            'plan' => 'spark',
            'status' => 'active',
        ]);

        $chart = ReportChart::create([
            'user_id' => $user->id,
            'title' => 'FORGE Weekly Heatmap',
            'report_type' => ForgeWeeklyHeatmap::REPORT_TYPE,
            'chart_type' => 'heatmap',
            'data' => ForgeWeeklyHeatmap::sample(),
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.charts.show', $chart))
            ->assertForbidden();
    }

    public function test_forge_heatmap_missing_fields_fails_safely(): void
    {
        $user = User::factory()->create([
            'plan' => 'forge',
            'status' => 'active',
        ]);

        $chart = ReportChart::create([
            'user_id' => $user->id,
            'title' => 'FORGE Weekly Heatmap',
            'report_type' => ForgeWeeklyHeatmap::REPORT_TYPE,
            'chart_type' => 'heatmap',
            'data' => [
                'report_type' => ForgeWeeklyHeatmap::REPORT_TYPE,
                'meta' => ['prepared_time' => 'Sunday 9:00 PM'],
            ],
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.charts.show', $chart))
            ->assertOk()
            ->assertSee('Forge weekly heatmap unavailable')
            ->assertSee('meta.system');
    }

    public function test_free_user_cannot_access_forge_monthly_badge(): void
    {
        $user = User::factory()->create([
            'plan' => 'free',
            'status' => 'free',
        ]);

        $badge = BadgeReport::create([
            'user_id' => $user->id,
            'title' => ForgeMonthlyBadgeReport::BADGE_MOMENTUM_ARCHITECT,
            'report_type' => ForgeMonthlyBadgeReport::REPORT_TYPE,
            'badge_name' => ForgeMonthlyBadgeReport::BADGE_MOMENTUM_ARCHITECT,
            'report_data' => ForgeMonthlyBadgeReport::sample(ForgeMonthlyBadgeReport::BADGE_MOMENTUM_ARCHITECT),
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.badges.show', $badge))
            ->assertForbidden();
    }

    public function test_spark_user_cannot_access_forge_monthly_badge(): void
    {
        $user = User::factory()->create([
            'plan' => 'spark',
            'status' => 'active',
        ]);

        $badge = BadgeReport::create([
            'user_id' => $user->id,
            'title' => ForgeMonthlyBadgeReport::BADGE_INFLUENCE_COMMANDER,
            'report_type' => ForgeMonthlyBadgeReport::REPORT_TYPE,
            'badge_name' => ForgeMonthlyBadgeReport::BADGE_INFLUENCE_COMMANDER,
            'report_data' => ForgeMonthlyBadgeReport::sample(ForgeMonthlyBadgeReport::BADGE_INFLUENCE_COMMANDER),
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.badges.show', $badge))
            ->assertForbidden();
    }

    public function test_forge_user_can_access_monthly_badge_report(): void
    {
        $user = User::factory()->create([
            'plan' => 'forge',
            'status' => 'active',
        ]);

        $payload = ForgeMonthlyBadgeReport::sample(ForgeMonthlyBadgeReport::BADGE_PRESENCE_DOMINATOR);

        $badge = BadgeReport::create([
            'user_id' => $user->id,
            'title' => ForgeMonthlyBadgeReport::BADGE_PRESENCE_DOMINATOR,
            'report_type' => ForgeMonthlyBadgeReport::REPORT_TYPE,
            'badge_name' => ForgeMonthlyBadgeReport::BADGE_PRESENCE_DOMINATOR,
            'report_data' => $payload,
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.badges.show', $badge))
            ->assertOk()
            ->assertSee(ForgeMonthlyBadgeReport::BADGE_PRESENCE_DOMINATOR)
            ->assertSee($payload['executive_summary']['headline'])
            ->assertSee($payload['next_month_focus']['text']);
    }

    public function test_forge_monthly_badge_missing_fields_fails_safely(): void
    {
        $user = User::factory()->create([
            'plan' => 'forge',
            'status' => 'active',
        ]);

        $badge = BadgeReport::create([
            'user_id' => $user->id,
            'title' => 'Broken Badge',
            'report_type' => ForgeMonthlyBadgeReport::REPORT_TYPE,
            'badge_name' => ForgeMonthlyBadgeReport::BADGE_MOMENTUM_ARCHITECT,
            'report_data' => [
                'badge_name' => ForgeMonthlyBadgeReport::BADGE_MOMENTUM_ARCHITECT,
                'meta' => ['prepared_time' => 'First Monday of the Month'],
            ],
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.badges.show', $badge))
            ->assertOk()
            ->assertSee('Forge monthly badge unavailable')
            ->assertSee('meta.system');
    }

    public function test_admin_can_store_and_preview_supported_structured_report_types(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'plan' => 'forge',
            'status' => 'active',
        ]);

        $user = User::factory()->create([
            'plan' => 'forge',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.reports.store'), [
                'type' => 'report',
                'user_id' => $user->id,
                'title' => 'FORGE — Weekly Brief',
                'report_type' => ForgeSundayWeeklyBrief::REPORT_TYPE,
                'report_json' => json_encode(ForgeSundayWeeklyBrief::sample(), JSON_THROW_ON_ERROR),
            ])
            ->assertRedirect();

        $this->actingAs($admin)
            ->post(route('admin.reports.store'), [
                'type' => 'chart',
                'user_id' => $user->id,
                'title' => 'FORGE Weekly Strategic Timeline',
                'report_type' => ForgeWeeklyTimeline::REPORT_TYPE,
                'report_json' => json_encode(ForgeWeeklyTimeline::sample(), JSON_THROW_ON_ERROR),
            ])
            ->assertRedirect();

        $this->actingAs($admin)
            ->post(route('admin.reports.store'), [
                'type' => 'chart',
                'user_id' => $user->id,
                'title' => 'FORGE Weekly Heatmap',
                'report_type' => ForgeWeeklyHeatmap::REPORT_TYPE,
                'report_json' => json_encode(ForgeWeeklyHeatmap::sample(), JSON_THROW_ON_ERROR),
            ])
            ->assertRedirect();

        $this->actingAs($admin)
            ->post(route('admin.reports.store'), [
                'type' => 'badge',
                'user_id' => $user->id,
                'title' => ForgeMonthlyBadgeReport::BADGE_MOMENTUM_ARCHITECT,
                'badge_name' => ForgeMonthlyBadgeReport::BADGE_MOMENTUM_ARCHITECT,
                'report_type' => ForgeMonthlyBadgeReport::REPORT_TYPE,
                'report_json' => json_encode(ForgeMonthlyBadgeReport::sample(ForgeMonthlyBadgeReport::BADGE_MOMENTUM_ARCHITECT), JSON_THROW_ON_ERROR),
            ])
            ->assertRedirect();

        $report = Report::where('report_type', ForgeSundayWeeklyBrief::REPORT_TYPE)->firstOrFail();
        $chart = ReportChart::where('report_type', ForgeWeeklyTimeline::REPORT_TYPE)->firstOrFail();
        $heatmap = ReportChart::where('report_type', ForgeWeeklyHeatmap::REPORT_TYPE)->firstOrFail();
        $badge = BadgeReport::where('report_type', ForgeMonthlyBadgeReport::REPORT_TYPE)->firstOrFail();

        $this->actingAs($admin)
            ->get(route('dashboard.reports.show', $report))
            ->assertOk()
            ->assertSee('FORGE Sunday Night Executive Report');

        $this->actingAs($admin)
            ->get(route('dashboard.charts.show', $chart))
            ->assertOk()
            ->assertSee('FORGE Weekly Strategic Timeline');

        $this->actingAs($admin)
            ->get(route('dashboard.charts.show', $heatmap))
            ->assertOk()
            ->assertSee('FORGE Weekly Heatmap');

        $this->actingAs($admin)
            ->get(route('dashboard.badges.show', $badge))
            ->assertOk()
            ->assertSee(ForgeMonthlyBadgeReport::BADGE_MOMENTUM_ARCHITECT);
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
            ->post('/billing/checkout/forge')
            ->assertRedirect('https://checkout.stripe.test/session');
    }

    public function test_pricing_labels_current_active_plan(): void
    {
        $user = User::factory()->create([
            'plan' => 'spark',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get('/pricing')
            ->assertOk()
            ->assertSee('Current Spark Plan')
            ->assertSee('Start Forge');
    }

    public function test_admin_can_edit_website_content_and_frontend_uses_it(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.website-content.index'))
            ->assertOk()
            ->assertSee('Home Content');

        $content = PageContent::query()
            ->where('page_key', 'home')
            ->where('section_key', 'hero')
            ->firstOrFail();

        $this->actingAs($admin)
            ->patch(route('admin.website-content.update', $content), [
                'title' => 'Launch-ready private guidance',
                'subtitle' => 'Edited by admin',
                'body' => 'This hero copy came from the page content table.',
                'button_text' => 'Begin',
                'button_url' => '/pricing',
                'sort_order' => 10,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.website-content.index', ['page' => 'home']));

        $this->get('/')
            ->assertOk()
            ->assertSee('Launch-ready private guidance')
            ->assertSee('Edited by admin')
            ->assertSee('This hero copy came from the page content table.')
            ->assertSee('Begin');
    }

    public function test_admin_can_update_profile_and_password(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Old Admin',
            'email' => 'old-admin@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.profile.edit'))
            ->assertOk()
            ->assertSee('Profile Details')
            ->assertSee('Change Password');

        $this->actingAs($admin)
            ->patch(route('admin.profile.update'), [
                'name' => 'New Admin',
                'email' => 'new-admin@example.com',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $admin->refresh();
        $this->assertSame('New Admin', $admin->name);
        $this->assertSame('new-admin@example.com', $admin->email);

        $this->actingAs($admin)
            ->patch(route('admin.profile.password'), [
                'current_password' => 'old-password',
                'new_password' => 'new-admin-password',
                'new_password_confirmation' => 'new-admin-password',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('new-admin-password', $admin->fresh()->password));
    }

    public function test_missing_page_content_falls_back_to_hardcoded_copy(): void
    {
        PageContent::query()->delete();

        $this->get('/pricing')
            ->assertOk()
            ->assertSee('Includes 1 free live call · 30 minutes', false);
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
