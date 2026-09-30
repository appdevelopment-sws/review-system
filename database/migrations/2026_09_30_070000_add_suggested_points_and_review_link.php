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
        Schema::table('campaigns', function (Blueprint $table) {
            if (!Schema::hasColumn('campaigns', 'suggested_points')) {
                $table->text('suggested_points')->nullable()->after('instructions');
            }
        });

        Schema::table('campaign_participations', function (Blueprint $table) {
            if (!Schema::hasColumn('campaign_participations', 'review_link')) {
                $table->string('review_link', 2000)->nullable()->after('review_text');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            if (Schema::hasColumn('campaigns', 'suggested_points')) {
                $table->dropColumn('suggested_points');
            }
        });

        Schema::table('campaign_participations', function (Blueprint $table) {
            if (Schema::hasColumn('campaign_participations', 'review_link')) {
                $table->dropColumn('review_link');
            }
        });
    }
};
