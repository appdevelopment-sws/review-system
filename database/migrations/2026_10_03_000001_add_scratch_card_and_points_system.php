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
        // Add reward points and scratch card state to campaign participations
        Schema::table('campaign_participations', function (Blueprint $table) {
            if (!Schema::hasColumn('campaign_participations', 'reward_points')) {
                $table->integer('reward_points')->default(0)->after('reward_amount');
            }
            if (!Schema::hasColumn('campaign_participations', 'is_scratched')) {
                $table->boolean('is_scratched')->default(false)->after('reward_points');
            }
            if (!Schema::hasColumn('campaign_participations', 'scratched_at')) {
                $table->timestamp('scratched_at')->nullable()->after('is_scratched');
            }
        });

        // Add points column to wallet transactions
        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('wallet_transactions', 'points')) {
                $table->integer('points')->default(0)->after('amount');
            }
        });

        // Add points and redemption fields to withdrawal requests
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('withdrawal_requests', 'redeemed_points')) {
                $table->integer('redeemed_points')->default(0)->after('amount');
            }
            if (!Schema::hasColumn('withdrawal_requests', 'gift_card_brand')) {
                $table->string('gift_card_brand')->nullable()->after('payout_type');
            }
            if (!Schema::hasColumn('withdrawal_requests', 'gift_card_email')) {
                $table->string('gift_card_email')->nullable()->after('gift_card_brand');
            }
            if (!Schema::hasColumn('withdrawal_requests', 'other_details')) {
                $table->text('other_details')->nullable()->after('gift_card_email');
            }
        });

        // Add points_balance to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'points_balance')) {
                $table->integer('points_balance')->default(0)->after('wallet_balance');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaign_participations', function (Blueprint $table) {
            $table->dropColumn(['reward_points', 'is_scratched', 'scratched_at']);
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropColumn(['points']);
        });

        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->dropColumn(['redeemed_points', 'gift_card_brand', 'gift_card_email', 'other_details']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['points_balance']);
        });
    }
};
