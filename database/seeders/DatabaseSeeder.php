<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\CampaignParticipation;
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
        $admin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => 'password123',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Seed Normal User 1 (Default Wallet starts at 0.00)
        $user1 = User::updateOrCreate(
            ['email' => 'user@user.com'],
            [
                'name' => 'Rahul Sharma',
                'password' => 'password123',
                'role' => 'user',
                'wallet_balance' => 0.00,
                'email_verified_at' => now(),
            ]
        );

        // Seed Normal User 2
        $user2 = User::updateOrCreate(
            ['email' => 'sarah.connor@example.com'],
            [
                'name' => 'Sarah Connor',
                'password' => 'password123',
                'role' => 'user',
                'wallet_balance' => 65.00,
                'email_verified_at' => now(),
            ]
        );

        // Task 1: Cafe Coffee Day Review (Earn ₹40)
        $task1 = Campaign::updateOrCreate(
            ['title' => 'Cafe Coffee Day Review'],
            [
                'description' => '<p>Visit your nearest Cafe Coffee Day or recall your latest order. Submit your honest Google Maps review rating with feedback about coffee quality and ambience.</p><ul><li>Write minimum 20 words review</li><li>Rate with 4 or 5 stars</li><li>Take a screenshot of submitted review and upload proof</li></ul>',
                'media_type' => 'image',
                'media_url' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&w=400&q=80',
                'redirect_url' => 'https://maps.google.com/?q=Cafe+Coffee+Day',
                'reward_amount' => 40.00,
                'participant_limit' => 500,
                'participants_count' => 124,
                'clicks_count' => 410,
                'impressions_count' => 1500,
                'status' => 'active',
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(25),
            ]
        );

        // Task 2: Mas Hospital Feedback (Earn ₹60)
        $task2 = Campaign::updateOrCreate(
            ['title' => 'Mas Hospital Feedback'],
            [
                'description' => '<p>Share your experience and health service feedback for Mas Hospital healthcare facilities. Help new patients make informed choices.</p><ul><li>Highlight clean facilities & staff courtesy</li><li>Upload review screenshot proof</li></ul>',
                'media_type' => 'image',
                'media_url' => 'https://images.unsplash.com/photo-1586773860418-d37222d8fce3?auto=format&fit=crop&w=400&q=80',
                'redirect_url' => 'https://maps.google.com/?q=Mas+Hospital',
                'reward_amount' => 60.00,
                'participant_limit' => 200,
                'participants_count' => 45,
                'clicks_count' => 190,
                'impressions_count' => 890,
                'status' => 'active',
                'start_date' => now()->subDays(3),
                'end_date' => now()->addDays(20),
            ]
        );

        // Task 3: New Fitness App Review (Earn ₹50)
        $task3 = Campaign::updateOrCreate(
            ['title' => 'New Fitness App Review'],
            [
                'description' => '<p>Download the workout tracker app from Play Store or App Store. Test the free workout plan for 5 minutes and write a positive review.</p><ul><li>Mention workout tracking accuracy</li><li>Upload Play Store review screenshot</li></ul>',
                'media_type' => 'image',
                'media_url' => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?auto=format&fit=crop&w=400&q=80',
                'redirect_url' => 'https://play.google.com/store/apps',
                'reward_amount' => 50.00,
                'participant_limit' => 300,
                'participants_count' => 92,
                'clicks_count' => 340,
                'impressions_count' => 1250,
                'status' => 'active',
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(30),
            ]
        );

        // Task 4: Gourmet Bistro Experience (Earn ₹75)
        $task4 = Campaign::updateOrCreate(
            ['title' => 'Gourmet Bistro Dining Review'],
            [
                'description' => '<p>Rate your fine dining experience, food presentation, and quick service at Gourmet Bistro.</p>',
                'media_type' => 'image',
                'media_url' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=400&q=80',
                'redirect_url' => 'https://maps.google.com/?q=Gourmet+Bistro',
                'reward_amount' => 75.00,
                'participant_limit' => 150,
                'participants_count' => 60,
                'clicks_count' => 210,
                'impressions_count' => 700,
                'status' => 'active',
                'start_date' => now()->subDays(1),
                'end_date' => now()->addDays(15),
            ]
        );

        // Task 5: Cloud Wallet & Banking App Testing (Earn ₹100)
        $task5 = Campaign::updateOrCreate(
            ['title' => 'FinTech UPI App Review'],
            [
                'description' => '<p>Review the instant UPI transfer speed and biometric login experience on Play Store.</p>',
                'media_type' => 'image',
                'media_url' => 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?auto=format&fit=crop&w=400&q=80',
                'redirect_url' => 'https://play.google.com/store/apps',
                'reward_amount' => 100.00,
                'participant_limit' => 100,
                'participants_count' => 18,
                'clicks_count' => 90,
                'impressions_count' => 450,
                'status' => 'active',
                'start_date' => now(),
            ]
        );
    }
}
