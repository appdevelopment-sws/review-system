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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('wallet_balance', 10, 2)->default(0.00)->after('role');
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('location_tag')->default('Online')->after('status'); // e.g. '2.3 km', '1.8 km', 'Online'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('wallet_balance');
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('location_tag');
        });
    }
};
