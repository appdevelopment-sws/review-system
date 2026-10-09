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
            $table->string('category')->nullable()->after('business_type');
            $table->string('category_id')->nullable()->after('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bounty_ai_profiles', function (Blueprint $table) {
            $table->dropColumn(['category', 'category_id']);
        });
    }
};