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

// View Routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', fn() => view('admin.users'))->name('users.index');
    Route::get('/announcements', fn() => view('admin.announcements'))->name('announcements.index');
    Route::get('/events', fn() => view('admin.events'))->name('events.index');
    Route::get('/analytics', fn() => view('admin.analytics'))->name('analytics');
    Route::get('/settings', fn() => view('admin.settings'))->name('settings.index');
});

// API Routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('api/admin')->name('admin.')->group(function () {
    
    // Dashboard Statistics
    Route::get('/statistics', [AdminController::class, 'getUserStatistics'])->name('statistics');
    Route::get('/recent-activity', [AdminController::class, 'getRecentActivity'])->name('recent-activity');
    Route::get('/analytics', [AdminController::class, 'getAnalytics'])->name('analytics');
    Route::get('/content-stats', [AdminController::class, 'getContentStats'])->name('content-stats');
    Route::post('/report', [AdminController::class, 'generateReport'])->name('report');
    
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
    
    // Content & Announcements Management - FIXED: Added all CRUD operations
    Route::prefix('announcements')->name('announcements.')->group(function () {
        // List all announcements (with filters)
        Route::get('/', [AdminController::class, 'getAnnouncements'])->name('index');
        
        // Get single announcement for view/edit
        Route::get('/{id}', [AdminController::class, 'getAnnouncement'])->name('show');
        
        // Create new announcement
        Route::post('/', [AdminController::class, 'storeAnnouncement'])->name('store');
        
        // Update announcement
        Route::patch('/{id}', [AdminController::class, 'updateAnnouncement'])->name('update');
        
        // Delete announcement
        Route::delete('/{id}', [AdminController::class, 'deleteAnnouncement'])->name('destroy');
        
        // Approve/reject (keep these)
        Route::patch('/{id}/approve', [AdminController::class, 'approveAnnouncement'])->name('approve');
        Route::patch('/{id}/reject', [AdminController::class, 'rejectAnnouncement'])->name('reject');
    });
    
    // Events Management
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [AdminController::class, 'getEvents'])->name('index');
        Route::delete('/{id}', [AdminController::class, 'deleteEvent'])->name('destroy');
    });
    
    // System Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AdminController::class, 'getSettings'])->name('index');
        Route::put('/', [AdminController::class, 'updateSettings'])->name('update');
    });
});