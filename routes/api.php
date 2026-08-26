<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiTaskController;
use App\Http\Controllers\Api\ApiWalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Authentication Routes
Route::post('/register', [ApiAuthController::class, 'register']);
Route::post('/login', [ApiAuthController::class, 'login']);

// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {
    // User Profile
    Route::get('/user', [ApiAuthController::class, 'me']);
    Route::post('/logout', [ApiAuthController::class, 'logout']);

    // Task / Campaign Routes
    Route::get('/tasks', [ApiTaskController::class, 'index']);
    Route::get('/tasks/{id}', [ApiTaskController::class, 'show']);
    Route::post('/tasks/{id}/submit', [ApiTaskController::class, 'submit']);
    Route::get('/my-tasks', [ApiTaskController::class, 'myTasks']);

    // Wallet & Withdrawal Routes
    Route::get('/wallet', [ApiWalletController::class, 'index']);
    Route::post('/wallet/withdraw', [ApiWalletController::class, 'withdraw']);
    Route::get('/wallet/withdrawals', [ApiWalletController::class, 'withdrawals']);

    // User Bank / UPI Payout Details
    Route::get('/user/payout-details', [ApiWalletController::class, 'getPayoutDetails']);
    Route::post('/user/payout-details', [ApiWalletController::class, 'savePayoutDetails']);
});
