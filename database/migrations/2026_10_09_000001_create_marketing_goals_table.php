<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('marketing_goals', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon')->default('users');
            $table->string('icon_bg_color')->default('#EEF2FF');
            $table->string('icon_color')->default('#3B82F6');
            $table->string('badge_text')->nullable();
            $table->boolean('is_instagram')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default initial goals matching the mobile onboarding design
        DB::table('marketing_goals')->insert([
            [
                'slug' => 'customers',
                'title' => 'Get more customers',
                'description' => 'Attract more customers to your business',
                'icon' => 'users',
                'icon_bg_color' => '#EEF2FF',
                'icon_color' => '#3B82F6',
                'badge_text' => 'High Demand',
                'is_instagram' => false,
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'reviews',
                'title' => 'Get more reviews',
                'description' => 'Build trust with positive customer reviews',
                'icon' => 'star',
                'icon_bg_color' => '#FEF3C7',
                'icon_color' => '#F59E0B',
                'badge_text' => 'Top Priority',
                'is_instagram' => false,
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'visibility',
                'title' => 'Improve visibility',
                'description' => 'Rank higher on Google and local search',
                'icon' => 'trending-up',
                'icon_bg_color' => '#DCFCE7',
                'icon_color' => '#10B981',
                'badge_text' => null,
                'is_instagram' => false,
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'social',
                'title' => 'Grow social media',
                'description' => 'Create and manage engaging content',
                'icon' => 'camera',
                'icon_bg_color' => '#FCE7F3',
                'icon_color' => '#E1306C',
                'badge_text' => 'Trending',
                'is_instagram' => true,
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_goals');
    }
};
