<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\CopyStudioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImageStudioController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Copy Studio
    Route::prefix('copy-studio')->name('copy-studio.')->group(function () {
        Route::get('/', [CopyStudioController::class, 'index'])->name('index');
        Route::post('/generate', [CopyStudioController::class, 'generate'])->name('generate');
        Route::get('/history', [CopyStudioController::class, 'history'])->name('history');
        Route::get('/jobs/{job}', [CopyStudioController::class, 'show'])->name('show');
        Route::get('/jobs/{job}/status', [CopyStudioController::class, 'status'])->name('status');
    });

    // Image Studio
    Route::prefix('image-studio')->name('image-studio.')->group(function () {
        Route::get('/', [ImageStudioController::class, 'index'])->name('index');
        Route::post('/generate', [ImageStudioController::class, 'generate'])->name('generate');
        Route::get('/gallery', [ImageStudioController::class, 'gallery'])->name('gallery');
        Route::get('/jobs/{job}', [ImageStudioController::class, 'show'])->name('show');
        Route::get('/jobs/{job}/status', [ImageStudioController::class, 'status'])->name('status');
    });

    // Billing
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/', [BillingController::class, 'index'])->name('index');
        Route::post('/checkout', [BillingController::class, 'checkout'])->name('checkout');
        Route::get('/success', [BillingController::class, 'success'])->name('success');
        Route::get('/portal', [BillingController::class, 'portal'])->name('portal');
        Route::get('/history', [BillingController::class, 'history'])->name('history');
    });
});

require __DIR__.'/auth.php';
