<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\User;
use App\Support\ForgeSundayWeeklyBrief;
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
    }
}
