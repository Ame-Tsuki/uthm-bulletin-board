<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| These routes are for admin dashboard backend operations.
| All routes require authentication and admin role.
|
*/

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard Statistics
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/statistics', [AdminController::class, 'getUserStatistics'])->name('statistics');
    Route::get('/recent-activity', [AdminController::class, 'getRecentActivity'])->name('recent-activity');
    
    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'getUsers'])->name('index');
        Route::get('/{id}', [AdminController::class, 'getUser'])->name('show');
        Route::post('/', [AdminController::class, 'createUser'])->name('store');
        Route::put('/{id}', [AdminController::class, 'updateUser'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'deleteUser'])->name('destroy');
        Route::patch('/{id}/toggle-verification', [AdminController::class, 'toggleUserVerification'])->name('toggle-verification');
        Route::post('/bulk-action', [AdminController::class, 'bulkAction'])->name('bulk-action');
    });
});

