<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@infinitesugar.local');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Infinite Sugar Admin'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'admin12345')),
                'plan' => 'forge',
                'status' => 'active',
                'role' => 'admin',
            ]
        );

        if (env('SEED_FORGE_PREVIEW_REPORT')) {
            $this->call(ForgeSundayReportPreviewSeeder::class);
        }
    }
}
