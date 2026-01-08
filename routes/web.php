<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Auth\CustomRegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ProfileController;

// Public Routes (No Auth Required)
Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('login', [CustomLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [CustomLoginController::class, 'login']);
    
    // Registration Routes
    Route::get('register', [CustomRegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [CustomRegisterController::class, 'register']);
    
    
});

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard')->with('verified', true);
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// Authenticated Routes (Require Login)
Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('/logout', [CustomLoginController::class, 'logout'])->name('logout');
    
    // Dashboard Routes (Role-based)
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        // Check if user is verified
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }
        
        // Redirect based on role
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'staff':
                return redirect()->route('staff.dashboard');
            case 'club_admin':
                return redirect()->route('club.dashboard');
            case 'student':
                return redirect()->route('student.dashboard');
            default:
                \Illuminate\Support\Facades\Auth::logout();
                return redirect('/login')->withErrors(['role' => 'Invalid user role']);
        }
    })->name('dashboard');

    // Profile Routes
    Route::get('/profile', function() {
        $user = auth()->user();
        return view('profile', compact('user'));
    })->name('profile');
    
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/settings', function() {
        $user = auth()->user(); // Pass the user just in case
        return view('settings', compact('user')); // Points to settings.blade.php
    })->name('settings');

    // Student Dashboard
    Route::get('/student/dashboard', function () {
        $user = auth()->user();
        return view('student.dashboard', compact('user'));
    })->name('student.dashboard')->middleware('role:student');

    // Staff Dashboard
    Route::get('/staff/dashboard', function () {
        $user = auth()->user();
        return view('staff.dashboard', compact('user'));
    })->name('staff.dashboard')->middleware('role:staff');

    // Club Dashboard
    Route::get('/club/dashboard', function () {
        $user = auth()->user();
        return view('club.dashboard', compact('user'));
    })->name('club.dashboard')->middleware('role:club_admin');

    // Announcement Routes
    Route::middleware(['verified'])->group(function () {
        Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/announcement/{id}', [AnnouncementController::class, 'show'])->name('announcements.show');
    });
});

// Announcement Routes
Route::resource('announcements', AnnouncementController::class);

// Additional announcement routes
Route::post('/announcements/{announcement}/archive', [AnnouncementController::class, 'archive'])
    ->name('announcements.archive');
Route::post('/announcements/{announcement}/publish', [AnnouncementController::class, 'publish'])
    ->name('announcements.publish');
Route::get('/announcements/published', [AnnouncementController::class, 'published'])
    ->name('announcements.published');
Route::get('/announcements/drafts', [AnnouncementController::class, 'drafts'])
    ->name('announcements.drafts');

// Admin Routes (Require Admin Role)
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/recent-activity', [AdminController::class, 'getRecentActivity'])->name('recent-activity');
    Route::get('/statistics', [AdminController::class, 'getStatistics'])->name('statistics');
    
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'getUsers'])->name('index');
        Route::post('/', [AdminController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/{id}', [AdminController::class, 'getUser'])->name('show');
        Route::put('/{id}', [AdminController::class, 'updateUser'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'deleteUser'])->name('destroy');
        Route::patch('/{id}/toggle-verification', [AdminController::class, 'toggleUserVerification'])->name('toggle-verification');
    });
});

// Test Route
Route::get('/test-route/{id}', function($id) {
    return "Test route works! ID: " . $id;
});

// Health Check Route
Route::get('/up', function () {
    return response()->json(['status' => 'ok']);
});
