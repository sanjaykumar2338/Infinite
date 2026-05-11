<?php

namespace Database\Seeders;

use App\Models\BadgeReport;
use App\Models\Report;
use App\Models\ReportChart;
use App\Models\User;
use App\Support\ForgeMonthlyBadgeReport;
use App\Support\ForgeSundayWeeklyBrief;
use App\Support\ForgeWeeklyHeatmap;
use App\Support\ForgeWeeklyTimeline;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ForgeSundayReportPreviewSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()
            ->when(env('ADMIN_EMAIL'), fn ($query, $email) => $query->where('email', $email))
            ->first();

        if (! $user) {
            $user = User::updateOrCreate(
                ['email' => 'forge-preview@example.com'],
                [
                    'name' => 'Forge Preview',
                    'password' => Hash::make('password'),
                    'plan' => 'forge',
                    'status' => 'active',
                ],
            );
        }

        Report::updateOrCreate(
            [
                'user_id' => $user->id,
                'title' => 'FORGE — Weekly Brief',
                'report_type' => ForgeSundayWeeklyBrief::REPORT_TYPE,
            ],
            [
                'summary' => 'Sunday 9 PM executive brief preview.',
                'report_data' => ForgeSundayWeeklyBrief::sample(),
                'period_start' => now()->startOfWeek()->toDateString(),
                'period_end' => now()->endOfWeek()->toDateString(),
                'published_at' => now(),
            ],
        );

        ReportChart::updateOrCreate(
            [
                'user_id' => $user->id,
                'title' => 'FORGE Weekly Heatmap',
                'report_type' => ForgeWeeklyHeatmap::REPORT_TYPE,
            ],
            [
                'chart_type' => 'heatmap',
                'data' => ForgeWeeklyHeatmap::sample(),
                'period_start' => now()->startOfWeek()->toDateString(),
                'period_end' => now()->endOfWeek()->toDateString(),
                'published_at' => now(),
            ],
        );

        ReportChart::updateOrCreate(
            [
                'user_id' => $user->id,
                'title' => 'FORGE Weekly Strategic Timeline',
                'report_type' => ForgeWeeklyTimeline::REPORT_TYPE,
            ],
            [
                'chart_type' => 'timeline',
                'data' => ForgeWeeklyTimeline::sample(),
                'period_start' => now()->startOfWeek()->toDateString(),
                'period_end' => now()->endOfWeek()->toDateString(),
                'published_at' => now(),
            ],
        );

        foreach (ForgeMonthlyBadgeReport::allowedBadges() as $badgeName) {
            BadgeReport::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'title' => $badgeName,
                    'report_type' => ForgeMonthlyBadgeReport::REPORT_TYPE,
                ],
                [
                    'summary' => 'Monthly Forge badge preview.',
                    'badge_name' => $badgeName,
                    'report_data' => ForgeMonthlyBadgeReport::sample($badgeName),
                    'month' => now()->startOfMonth()->toDateString(),
                    'published_at' => now(),
                ],
            );
        }
    }
}
