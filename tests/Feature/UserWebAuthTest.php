<?php

namespace Tests\Feature;

use App\Models\BadgeReport;
use App\Models\Report;
use App\Models\ReportChart;
use App\Models\User;
use App\Services\FirebaseAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('Monthly Confidence Badge');
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
}
