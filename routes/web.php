<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TahunExpoController;
use Inertia\Inertia;
use function Pest\Laravel\get;

// Guest User Routes
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.post');

// Root redirect - Check if user is authenticated
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Admin Routes - Using isLogin middleware
Route::middleware(['isLogin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // Tahun Expo
    Route::get('/tahun-expo', [TahunExpoController::class, 'showTahunExpo'])->name('tahun-expo');
    Route::post('/tahun-expo', [TahunExpoController::class, 'insertTahunExpo'])->name('tahun-expo.insert');
    Route::put('/tahun-expo/{id}', [TahunExpoController::class, 'updateTahunExpo'])->name('tahun-expo.update');
    Route::delete('/tahun-expo/{id}', [TahunExpoController::class, 'deleteTahunExpo'])->name('tahun-expo.delete');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
