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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('media_type')->default('image'); // image, video, none
            $table->string('media_url')->nullable();
            $table->decimal('reward_amount', 10, 2)->default(0.00); // Reward per user participation
            $table->integer('participant_limit')->default(100); // Max users who can avail
            $table->integer('participants_count')->default(0); // Current users availed
            $table->string('status')->default('active'); // active, paused, completed, draft
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
