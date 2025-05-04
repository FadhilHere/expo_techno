<?php

use App\Http\Controllers\Admin\KategoriTenantController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\User\PreOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TahunExpoController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\AboutusController;
use Inertia\Inertia;
use function Pest\Laravel\get;

// Guest User Routes
Route::get('/', [HomeController::class, 'showHomeView'])->name('home');
Route::get('/about', [AboutusController::class, 'showAboutusView'])->name('about');
Route::get('/tenant/{id}', [HomeController::class, 'showTenantDetail'])->name('tenant.detail');
Route::post('/pre-order', [PreOrderController::class, 'InsertPreOrder'])->name('pre-order.insert');

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.post');

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
    // Kategori Tenant
    Route::get('/kategori-tenant', [KategoriTenantController::class, 'showKategoriTenant'])->name('kategori-tenant');
    Route::post('/kategori-tenant', [KategoriTenantController::class, 'insertKategoriTenant'])->name('kategori-tenant.insert');
    Route::put('/kategori-tenant/{id}', [KategoriTenantController::class, 'updateKategoriTenant'])->name('kategori-tenant.update');
    Route::delete('/kategori-tenant/{id}', [KategoriTenantController::class, 'deleteKategoriTenant'])->name('kategori-tenant.delete');
    // Tenant Routes
    Route::get('/tenant', [TenantController::class, 'showTenant'])->name('tenant');
    Route::post('/tenant', [TenantController::class, 'insertTenant'])->name('tenant.insert');
    Route::put('/tenant/{id}', [TenantController::class, 'updateTenant'])->name('tenant.update');
    Route::delete('/tenant/{id}', [TenantController::class, 'deleteTenant'])->name('tenant.delete');
    // Products Routes
    Route::get('/tenant/{tenantId}/products', [ProductController::class, 'showProducts'])->name('tenant.products');
    Route::post('/tenant/{tenantId}/products', [ProductController::class, 'insertProduct'])->name('tenant.products.insert');
    Route::put('/tenant/{tenantId}/products/{productId}', [ProductController::class, 'updateProduct'])->name('tenant.products.update');
    Route::delete('/tenant/{tenantId}/products/{productId}', [ProductController::class, 'deleteProduct'])->name('tenant.products.delete');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
