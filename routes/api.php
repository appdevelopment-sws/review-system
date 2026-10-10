<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiBusinessCampaignController;
use App\Http\Controllers\Api\ApiTaskController;
use App\Http\Controllers\Api\ApiWalletController;
use App\Http\Controllers\Api\ApiBountyAiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Authentication Routes
Route::post('/register', [ApiAuthController::class, 'register']);
Route::post('/login', [ApiAuthController::class, 'login']);
Route::get('/marketing-goals', [ApiTaskController::class, 'marketingGoals']);

// Bounty AI Onboarding & Business Profile Routes
Route::get('/bounty-ai/config', [ApiBountyAiController::class, 'config']);
Route::get('/bounty-ai/business-info', [ApiBountyAiController::class, 'getBusinessInfo']);
Route::post('/bounty-ai/onboarding-step1', [ApiBountyAiController::class, 'saveStep1']);
Route::post('/bounty-ai/business-info', [ApiBountyAiController::class, 'saveStep2']);
Route::get('/bounty-ai/categories', [ApiBountyAiController::class, 'categories']);
Route::post('/bounty-ai/category', [ApiBountyAiController::class, 'saveStep3Category']);
Route::post('/bounty-ai/location', [ApiBountyAiController::class, 'saveStep4Location']);
Route::post('/bounty-ai/google-connect', [ApiBountyAiController::class, 'saveStep5GoogleConnect']);
Route::post('/bounty-ai/google-location', [ApiBountyAiController::class, 'saveStep6GoogleLocation']);

// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {
    // User Profile
    Route::get('/user', [ApiAuthController::class, 'me']);
    Route::post('/logout', [ApiAuthController::class, 'logout']);

    // Business Campaign Management Routes
    Route::get('/business/campaigns', [ApiBusinessCampaignController::class, 'index']);
    Route::post('/business/campaigns', [ApiBusinessCampaignController::class, 'store']);
    Route::get('/business/campaigns/{id}', [ApiBusinessCampaignController::class, 'show']);

    // Categories Route
    Route::get('/categories', [ApiTaskController::class, 'categories']);

    // Task / Campaign Routes
    Route::get('/tasks', [ApiTaskController::class, 'index']);
    Route::get('/tasks/{id}', [ApiTaskController::class, 'show']);
    Route::post('/tasks/{id}/submit', [ApiTaskController::class, 'submit']);
    Route::get('/my-tasks', [ApiTaskController::class, 'myTasks']);

    // Scratch Card Reward / Benefit Flow (Task Approved -> Scratch Card -> Scratch -> Points Revealed -> Wallet Credit)
    Route::post('/tasks/{id}/scratch', [ApiTaskController::class, 'scratchCard']);
    Route::get('/wallet/scratch-cards', [ApiWalletController::class, 'scratchCards']);

    // Wallet & Withdrawal Routes (Available, Pending, Earned, Used/Withdrawn Points + UPI, Bank, Gift Cards, Other)
    Route::get('/wallet', [ApiWalletController::class, 'index']);
    Route::post('/wallet/withdraw', [ApiWalletController::class, 'withdraw']);
    Route::get('/wallet/withdrawals', [ApiWalletController::class, 'withdrawals']);

    // User Bank / UPI Payout Details
    Route::get('/user/payout-details', [ApiWalletController::class, 'getPayoutDetails']);
    Route::post('/user/payout-details', [ApiWalletController::class, 'savePayoutDetails']);
});
