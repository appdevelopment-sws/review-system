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
            if (!Schema::hasColumn('bounty_ai_profiles', 'google_refresh_token')) {
                $table->text('google_refresh_token')->nullable()->after('google_avatar_url');
            }
            if (!Schema::hasColumn('bounty_ai_profiles', 'google_access_token')) {
                $table->text('google_access_token')->nullable()->after('google_refresh_token');
            }
            if (!Schema::hasColumn('bounty_ai_profiles', 'google_token_expires_at')) {
                $table->timestamp('google_token_expires_at')->nullable()->after('google_access_token');
            }
            if (!Schema::hasColumn('bounty_ai_profiles', 'google_account_id')) {
                $table->string('google_account_id')->nullable()->after('google_token_expires_at');
            }
            if (!Schema::hasColumn('bounty_ai_profiles', 'google_scope')) {
                $table->text('google_scope')->nullable()->after('google_account_id');
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
                'google_refresh_token',
                'google_access_token',
                'google_token_expires_at',
                'google_account_id',
                'google_scope',
            ]);
        });
    }
};
