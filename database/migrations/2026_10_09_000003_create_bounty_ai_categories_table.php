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
        Schema::create('bounty_ai_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->default('storefront');
            $table->string('icon_bg_color')->default('#FFFBEB');
            $table->string('icon_color')->default('#D97706');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('bounty_ai_categories')->insert([
            [
                'category_key' => 'retail_store',
                'name' => 'Retail Store',
                'description' => 'General retail, shops and showrooms',
                'icon' => 'shopping_bag',
                'icon_bg_color' => '#FFFBEB',
                'icon_color' => '#D97706',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_key' => 'restaurant_food',
                'name' => 'Restaurant & Food',
                'description' => 'Cafes, restaurants, cloud kitchens',
                'icon' => 'restaurant',
                'icon_bg_color' => '#FFF7ED',
                'icon_color' => '#EA580C',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_key' => 'healthcare',
                'name' => 'Healthcare',
                'description' => 'Clinics, hospitals, pharmacies',
                'icon' => 'medical_services',
                'icon_bg_color' => '#ECFDF5',
                'icon_color' => '#059669',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_key' => 'fitness_wellness',
                'name' => 'Fitness & Wellness',
                'description' => 'Gyms, yoga, salons, spas',
                'icon' => 'fitness_center',
                'icon_bg_color' => '#F5F3FF',
                'icon_color' => '#7C3AED',
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_key' => 'education',
                'name' => 'Education',
                'description' => 'Coaching, schools, colleges, training',
                'icon' => 'school',
                'icon_bg_color' => '#EFF6FF',
                'icon_color' => '#2563EB',
                'sort_order' => 5,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_key' => 'real_estate',
                'name' => 'Real Estate',
                'description' => 'Builders, property dealers, rentals',
                'icon' => 'apartment',
                'icon_bg_color' => '#FFF1F2',
                'icon_color' => '#E11D48',
                'sort_order' => 6,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_key' => 'automotive',
                'name' => 'Automotive',
                'description' => 'Car & bike services, showrooms, repair',
                'icon' => 'directions_car',
                'icon_bg_color' => '#FEFCE8',
                'icon_color' => '#CA8A04',
                'sort_order' => 7,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_key' => 'it_services',
                'name' => 'IT & Services',
                'description' => 'Software, IT services, consultancy',
                'icon' => 'laptop_mac',
                'icon_bg_color' => '#F0F9FF',
                'icon_color' => '#0284C7',
                'sort_order' => 8,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_key' => 'home_services',
                'name' => 'Home Services',
                'description' => 'AC repair, plumbing, electrical, cleaning',
                'icon' => 'build',
                'icon_bg_color' => '#FAF5FF',
                'icon_color' => '#9333EA',
                'sort_order' => 9,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_key' => 'ecommerce',
                'name' => 'E-commerce',
                'description' => 'Online stores, D2C brands',
                'icon' => 'shopping_cart',
                'icon_bg_color' => '#F0FDF4',
                'icon_color' => '#16A34A',
                'sort_order' => 10,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_key' => 'professional_services',
                'name' => 'Professional Services',
                'description' => 'CA, legal, finance, consultants',
                'icon' => 'work',
                'icon_bg_color' => '#FEF2F2',
                'icon_color' => '#DC2626',
                'sort_order' => 11,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_key' => 'other',
                'name' => 'Other',
                'description' => 'Not listed? Select this option',
                'icon' => 'more_horiz',
                'icon_bg_color' => '#F1F5F9',
                'icon_color' => '#64748B',
                'sort_order' => 12,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bounty_ai_categories');
    }
};