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
        if ($email = env('ADMIN_EMAIL')) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => env('ADMIN_NAME', 'Infinite Sugar Admin'),
                    'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                    'plan' => 'forge',
                    'status' => 'active',
                    'role' => 'admin',
                ]
            );
        }
    }
}
