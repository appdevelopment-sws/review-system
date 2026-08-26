<?php

use App\Http\Controllers\Admin\AdminAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public Track Click & Redirect Route
Route::get('/campaigns/{id}/redirect', [AdminAuthController::class, 'trackClick'])->name('campaign.redirect');

// Admin Panel Routes
Route::prefix('admin')->group(function () {
    // Guest Admin Routes
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');

    // Authenticated Admin Routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/dashboard', [AdminAuthController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/users', [AdminAuthController::class, 'users'])->name('admin.users');
        Route::get('/users/{id}', [AdminAuthController::class, 'showUser'])->name('admin.users.show');
        
        // Categories Management
        Route::get('/categories', [AdminAuthController::class, 'categories'])->name('admin.categories');
        Route::post('/categories', [AdminAuthController::class, 'storeCategory'])->name('admin.categories.store');
        Route::put('/categories/{id}', [AdminAuthController::class, 'updateCategory'])->name('admin.categories.update');
        Route::delete('/categories/{id}', [AdminAuthController::class, 'destroyCategory'])->name('admin.categories.destroy');

        // Campaigns & Reports
        Route::get('/campaigns', [AdminAuthController::class, 'campaigns'])->name('admin.campaigns');
        Route::post('/campaigns', [AdminAuthController::class, 'storeCampaign'])->name('admin.campaigns.store');
        Route::put('/campaigns/{id}', [AdminAuthController::class, 'updateCampaign'])->name('admin.campaigns.update');
        Route::delete('/campaigns/{id}', [AdminAuthController::class, 'destroyCampaign'])->name('admin.campaigns.destroy');
        Route::get('/campaigns/{id}/report', [AdminAuthController::class, 'campaignReport'])->name('admin.campaigns.report');

        // Conversions & Review Screenshot Submissions
        Route::get('/conversions', [AdminAuthController::class, 'conversions'])->name('admin.conversions');
        Route::post('/conversions/{id}/status', [AdminAuthController::class, 'updateConversionStatus'])->name('admin.conversions.status');

        // Withdrawal Requests & Payout Processing
        Route::get('/withdrawals', [AdminAuthController::class, 'withdrawals'])->name('admin.withdrawals');
        Route::post('/withdrawals/{id}/status', [AdminAuthController::class, 'updateWithdrawalStatus'])->name('admin.withdrawals.status');

        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });
    });
});
