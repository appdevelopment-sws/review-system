<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Admin User
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => 'password123',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Seed Normal User
        User::updateOrCreate(
            ['email' => 'user@user.com'],
            [
                'name' => 'Normal User',
                'password' => 'password123',
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );
    }
}
