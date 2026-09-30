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
            if (!Schema::hasColumn('campaigns', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('campaigns', 'platform')) {
                $table->string('platform')->nullable()->after('category_id');
            }
            if (!Schema::hasColumn('campaigns', 'instructions')) {
                $table->text('instructions')->nullable()->after('description');
            }
            if (!Schema::hasColumn('campaigns', 'payment_info')) {
                $table->text('payment_info')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            if (Schema::hasColumn('campaigns', 'user_id')) {
                try {
                    $table->dropForeign(['user_id']);
                } catch (\Throwable $e) {}
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('campaigns', 'platform')) {
                $table->dropColumn('platform');
            }
            if (Schema::hasColumn('campaigns', 'instructions')) {
                $table->dropColumn('instructions');
            }
            if (Schema::hasColumn('campaigns', 'payment_info')) {
                $table->dropColumn('payment_info');
            }
        });
    }
};
