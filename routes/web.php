<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

// Guests are sent to /login by the auth middleware on /dashboard.
Route::redirect('/', '/dashboard');

/*
|--------------------------------------------------------------------------
| Shared authenticated routes (Admin and Staff)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Role-aware redirect to /admin/dashboard or /staff/dashboard.
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Admin area — /admin/*, route names admin.*
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Admin sees and manages every product (archived view registered before the resource
        // so "archived" is not captured as a {product} id).
        Route::get('/products/archived', [ProductController::class, 'archived'])->name('products.archived');
        Route::patch('/products/{productId}/restore', [ProductController::class, 'restore'])
            ->whereNumber('productId')->name('products.restore');
        Route::resource('products', ProductController::class);

        // Stock movements are audit records: list, create and store only.
        Route::resource('stock-movements', StockMovementController::class)->only(['index', 'create', 'store']);

        // Reference data (admin only).
        Route::resource('categories', CategoryController::class);
        Route::resource('suppliers', SupplierController::class);

        // User management: no hard delete — users are deactivated to preserve audit history.
        Route::resource('users', UserManagementController::class)->except(['destroy']);
        Route::patch('/users/{user}/status', [UserManagementController::class, 'toggleStatus'])->name('users.status');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

/*
|--------------------------------------------------------------------------
| Staff area — /staff/*, route names staff.*
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');

        // Same controller; listing is scoped to the staff member's own products
        // and every record action is checked by ProductPolicy (ownership).
        Route::resource('products', ProductController::class);

        // Movements on the staff member's own products only.
        Route::resource('stock-movements', StockMovementController::class)->only(['index', 'create', 'store']);
    });

require __DIR__.'/auth.php';
