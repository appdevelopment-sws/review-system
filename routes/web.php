<?php

use App\Http\Controllers\Admin\AdminAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Admin Panel Routes
Route::prefix('admin')->group(function () {
    // Guest Admin Routes
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');

    // Authenticated Admin Routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/dashboard', [AdminAuthController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/users', [AdminAuthController::class, 'users'])->name('admin.users');
        Route::get('/campaigns', [AdminAuthController::class, 'campaigns'])->name('admin.campaigns');
        Route::post('/campaigns', [AdminAuthController::class, 'storeCampaign'])->name('admin.campaigns.store');
        Route::put('/campaigns/{id}', [AdminAuthController::class, 'updateCampaign'])->name('admin.campaigns.update');
        Route::delete('/campaigns/{id}', [AdminAuthController::class, 'destroyCampaign'])->name('admin.campaigns.destroy');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });
    });
});
