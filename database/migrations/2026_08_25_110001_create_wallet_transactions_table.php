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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['credit', 'debit'])->default('credit'); // credit = reward added, debit = withdrawal
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->string('title'); // e.g. "Task Reward: Cafe Coffee Day Review"
            $table->text('description')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable(); // campaign_participation id or withdrawal id
            $table->string('reference_type')->nullable(); // 'campaign_participation', 'withdrawal'
            $table->string('status')->default('completed'); // completed, pending, rejected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
