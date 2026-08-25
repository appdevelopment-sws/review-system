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

        // Seed Normal User 1
        $user1 = User::updateOrCreate(
            ['email' => 'user@user.com'],
            [
                'name' => 'John Doe',
                'password' => 'password123',
                'role' => 'user',
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
                'email_verified_at' => now(),
            ]
        );

        // Seed Demo Campaign 1
        $campaign1 = Campaign::updateOrCreate(
            ['title' => 'Summer App Feedback & Review Campaign'],
            [
                'description' => '<p>Participate in our <strong>Summer Feedback Program</strong>! Share your honest review of our app features and earn a instant reward credit.</p><ul><li>Submit app review screenshot</li><li>Provide 2 suggestions for improvement</li></ul>',
                'media_type' => 'image',
                'media_url' => 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=600&q=80',
                'redirect_url' => 'https://play.google.com/store/apps',
                'reward_amount' => 15.00,
                'participant_limit' => 250,
                'participants_count' => 84,
                'clicks_count' => 340,
                'impressions_count' => 1200,
                'status' => 'active',
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(20),
            ]
        );

        // Seed Demo Campaign 2
        $campaign2 = Campaign::updateOrCreate(
            ['title' => 'VIP Social Media Video Challenge'],
            [
                'description' => '<h3>Create a short 30-sec Video Showcase!</h3><p>Upload a video testing our core features on Instagram or TikTok with hashtag <code>#ReviewApp2026</code> to claim your bonus payout!</p>',
                'media_type' => 'video',
                'media_url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
                'redirect_url' => 'https://instagram.com',
                'reward_amount' => 50.00,
                'participant_limit' => 50,
                'participants_count' => 31,
                'clicks_count' => 180,
                'impressions_count' => 850,
                'status' => 'active',
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(14),
            ]
        );

        // Seed Demo Campaign 3
        $campaign3 = Campaign::updateOrCreate(
            ['title' => 'Early Access Beta Tester Rewards'],
            [
                'description' => '<p>Help us test the new <em>v2.0 Beta release</em>. Report bug findings to receive cashback straight to your wallet.</p>',
                'media_type' => 'image',
                'media_url' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=600&q=80',
                'redirect_url' => 'https://testflight.apple.com',
                'reward_amount' => 25.00,
                'participant_limit' => 100,
                'participants_count' => 100,
                'clicks_count' => 410,
                'impressions_count' => 1500,
                'status' => 'completed',
                'start_date' => now()->subDays(20),
                'end_date' => now()->subDays(1),
            ]
        );

        // Seed Sample Participations (Submissions with Review Shared SS)
        CampaignParticipation::updateOrCreate(
            [
                'campaign_id' => $campaign1->id,
                'user_id' => $user1->id,
            ],
            [
                'proof_image' => 'https://images.unsplash.com/photo-1616469829941-c7200edec809?auto=format&fit=crop&w=800&q=80',
                'review_text' => 'Loved the app UI! Rated 5 stars on Play Store with detailed feedback.',
                'status' => 'pending',
                'reward_amount' => 15.00,
                'submitted_at' => now()->subHours(2),
            ]
        );

        CampaignParticipation::updateOrCreate(
            [
                'campaign_id' => $campaign2->id,
                'user_id' => $user2->id,
            ],
            [
                'proof_image' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?auto=format&fit=crop&w=800&q=80',
                'review_text' => 'Posted video story on Instagram tagged #ReviewApp2026. Here is the screenshot proof.',
                'status' => 'approved',
                'reward_amount' => 50.00,
                'submitted_at' => now()->subDays(1),
            ]
        );

        CampaignParticipation::updateOrCreate(
            [
                'campaign_id' => $campaign1->id,
                'user_id' => $user2->id,
            ],
            [
                'proof_image' => 'https://images.unsplash.com/photo-1555774698-0b77e0d5fac6?auto=format&fit=crop&w=800&q=80',
                'review_text' => 'Great experience using the summer app promo feature!',
                'status' => 'approved',
                'reward_amount' => 15.00,
                'submitted_at' => now()->subDays(2),
            ]
        );
    }
}
