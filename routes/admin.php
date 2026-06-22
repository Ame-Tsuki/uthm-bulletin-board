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

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // View Pages
    Route::get('/users', fn() => view('admin.users'))->name('users.view');
    Route::get('/announcements', fn() => view('admin.announcements'))->name('announcements.index');
    Route::get('/events', fn() => view('admin.events'))->name('events.index');
    
    Route::get('/calendar', function () {
        $user = auth()->user();
        return view('admin.calendar', compact('user'));
    })->name('calendar');
    
    Route::get('/analytics', fn() => view('admin.analytics'))->name('analytics');
    Route::view('/settings', 'admin.settings')->name('settings.index');

    // Dashboard Statistics
    Route::get('/statistics', [AdminController::class, 'getUserStatistics'])->name('statistics');
    Route::get('/recent-activity', [AdminController::class, 'getRecentActivity'])->name('recent-activity');
    Route::get('/analytics-data', [AdminController::class, 'getAnalytics'])->name('analytics.data');
    Route::get('/content-stats', [AdminController::class, 'getContentStats'])->name('content-stats');
    Route::post('/report', [AdminController::class, 'generateReport'])->name('report');

    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/list', [AdminController::class, 'getUsers'])->name('index');
        Route::get('/data/{id}', [AdminController::class, 'getUser'])->name('show');
        Route::post('/create', [AdminController::class, 'createUser'])->name('store');
        Route::put('/update/{id}', [AdminController::class, 'updateUser'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'deleteUser'])->name('destroy');
        Route::patch('/{id}/toggle-verification', [AdminController::class, 'toggleUserVerification'])->name('toggle-verification');
        Route::patch('/{id}/toggle-ban', [AdminController::class, 'toggleUserBan'])->name('toggle-ban');
        Route::post('/bulk-action', [AdminController::class, 'bulkAction'])->name('bulk-action');
    });

    // Announcements Management
    Route::prefix('announcements')->name('announcements.')->group(function () {
        // Data endpoints for AJAX (more specific routes first)
        Route::get('/list', [AdminController::class, 'getAnnouncements'])->name('list');
        Route::get('/data/{id}', [AdminController::class, 'getAnnouncement'])->name('show');
        Route::patch('/{id}/approve', [AdminController::class, 'approveAnnouncement'])->name('approve');
        Route::patch('/{id}/reject', [AdminController::class, 'rejectAnnouncement'])->name('reject');
        
        // RESTful endpoints (generic routes last)
        Route::post('/', [AdminController::class, 'storeAnnouncement'])->name('store');
        Route::match(['PUT', 'PATCH'], '/{id}', [AdminController::class, 'updateAnnouncement'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'deleteAnnouncement'])->name('destroy');
    });

    // Events Management
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/list', [AdminController::class, 'getEvents'])->name('index');
        Route::delete('/{id}', [AdminController::class, 'deleteEvent'])->name('destroy');
    });

    // System Settings Data
    Route::put('/settings/update', [AdminController::class, 'updateSettings'])->name('settings.update');
});
