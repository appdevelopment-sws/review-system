<?php

namespace Database\Seeders;

use App\Models\Campaign;
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

        // Seed Demo Campaigns
        Campaign::updateOrCreate(
            ['title' => 'Summer App Feedback & Review Campaign'],
            [
                'description' => '<p>Participate in our <strong>Summer Feedback Program</strong>! Share your honest review of our app features and earn a instant reward credit.</p><ul><li>Submit app review screenshot</li><li>Provide 2 suggestions for improvement</li></ul>',
                'media_type' => 'image',
                'media_url' => 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=600&q=80',
                'reward_amount' => 15.00,
                'participant_limit' => 250,
                'participants_count' => 84,
                'status' => 'active',
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(20),
            ]
        );

        Campaign::updateOrCreate(
            ['title' => 'VIP Social Media Video Challenge'],
            [
                'description' => '<h3>Create a short 30-sec Video Showcase!</h3><p>Upload a video testing our core features on Instagram or TikTok with hashtag <code>#ReviewApp2026</code> to claim your bonus payout!</p>',
                'media_type' => 'video',
                'media_url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
                'reward_amount' => 50.00,
                'participant_limit' => 50,
                'participants_count' => 31,
                'status' => 'active',
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(14),
            ]
        );

        Campaign::updateOrCreate(
            ['title' => 'Early Access Beta Tester Rewards'],
            [
                'description' => '<p>Help us test the new <em>v2.0 Beta release</em>. Report bug findings to receive cashback straight to your wallet.</p>',
                'media_type' => 'image',
                'media_url' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=600&q=80',
                'reward_amount' => 25.00,
                'participant_limit' => 100,
                'participants_count' => 100,
                'status' => 'completed',
                'start_date' => now()->subDays(20),
                'end_date' => now()->subDays(1),
            ]
        );
    }
}
