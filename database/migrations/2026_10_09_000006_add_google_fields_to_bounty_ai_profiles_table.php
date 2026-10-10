<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bounty_ai_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('bounty_ai_profiles', 'is_google_connected')) {
                $table->boolean('is_google_connected')->default(false)->after('is_gps_detected');
            }
            if (!Schema::hasColumn('bounty_ai_profiles', 'google_account_email')) {
                $table->string('google_account_email')->nullable()->after('is_google_connected');
            }
            if (!Schema::hasColumn('bounty_ai_profiles', 'google_account_name')) {
                $table->string('google_account_name')->nullable()->after('google_account_email');
            }
            if (!Schema::hasColumn('bounty_ai_profiles', 'google_avatar_url')) {
                $table->text('google_avatar_url')->nullable()->after('google_account_name');
            }
            if (!Schema::hasColumn('bounty_ai_profiles', 'google_location_id')) {
                $table->string('google_location_id')->nullable()->after('google_avatar_url');
            }
            if (!Schema::hasColumn('bounty_ai_profiles', 'google_location_title')) {
                $table->string('google_location_title')->nullable()->after('google_location_id');
            }
            if (!Schema::hasColumn('bounty_ai_profiles', 'google_location_address')) {
                $table->text('google_location_address')->nullable()->after('google_location_title');
            }
            if (!Schema::hasColumn('bounty_ai_profiles', 'google_rating')) {
                $table->decimal('google_rating', 3, 2)->nullable()->default(4.80)->after('google_location_address');
            }
            if (!Schema::hasColumn('bounty_ai_profiles', 'google_reviews_count')) {
                $table->integer('google_reviews_count')->nullable()->default(142)->after('google_rating');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bounty_ai_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'is_google_connected',
                'google_account_email',
                'google_account_name',
                'google_avatar_url',
                'google_location_id',
                'google_location_title',
                'google_location_address',
                'google_rating',
                'google_reviews_count',
            ]);
        });
    }
};
