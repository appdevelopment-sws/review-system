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
            $table->decimal('latitude', 10, 7)->nullable()->after('pincode');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('landmark')->nullable()->after('longitude');
            $table->boolean('is_gps_detected')->default(false)->after('landmark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bounty_ai_profiles', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'landmark', 'is_gps_detected']);
        });
    }
};
