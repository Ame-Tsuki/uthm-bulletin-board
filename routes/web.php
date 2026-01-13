<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Auth\CustomRegisterController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
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

// Password Reset Routes
Route::get('password/reset', [PasswordResetLinkController::class, 'create'])->name('password.request');
Route::post('password/email', [PasswordResetLinkController::class, 'store'])->name('password.email');
Route::get('password/reset/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
Route::post('password/reset', [NewPasswordController::class, 'store'])->name('password.update');

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
        $user = auth()->user();
        return view('settings', compact('user'));
    })->name('settings');

    // Student Dashboard & Calendar
    Route::middleware('role:student')->group(function () {
        Route::get('/student/dashboard', function () {
            $user = auth()->user();
            return view('student.dashboard', compact('user'));
        })->name('student.dashboard');
        
        Route::get('/student/calendar', function () {
            $user = auth()->user();
            $events = [];
            $academicYear = date('Y');
            $currentMonth = date('n');
            $currentYear = date('Y');
            
            return view('student.calendar', compact('user', 'events', 'academicYear', 'currentMonth', 'currentYear'));
        })->name('student.calendar');
    });

    // Staff Dashboard & Calendar
    Route::middleware('role:staff')->group(function () {
        Route::get('/staff/dashboard', function () {
            $user = auth()->user();
            return view('staff.dashboard', compact('user'));
        })->name('staff.dashboard');
        
        Route::get('/staff/calendar', function () {
            $user = auth()->user();
            return view('staff.calendar', compact('user'));
        })->name('staff.calendar');
    });

    // Club Dashboard & Calendar
    Route::middleware('role:club_admin')->group(function () {
        Route::get('/club/dashboard', function () {
            $user = auth()->user();
            return view('club.dashboard', compact('user'));
        })->name('club.dashboard');
        
        Route::get('/club/calendar', function () {
            $user = auth()->user();
            return view('club.calendar', compact('user'));
        })->name('club.calendar');
    });

    // Admin Dashboard & Calendar
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/calendar', function () {
            $user = auth()->user();
            return view('admin.calendar', compact('user'));
        })->name('admin.calendar');
    });

    // General Calendar Route (for all authenticated users)
    Route::get('/calendar', function () {
        $user = auth()->user();
        
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.calendar');
            case 'staff':
                return redirect()->route('staff.calendar');
            case 'club_admin':
                return redirect()->route('club.calendar');
            case 'student':
                return redirect()->route('student.calendar');
            default:
                return view('student.calendar', compact('user'));
        }
    })->name('calendar');

    // ============================================
    // ANNOUNCEMENT ROUTES - OUTSIDE ADMIN PREFIX
    // ============================================
    
    Route::middleware(['auth', 'verified'])->group(function () {
        // All announcement GET routes
        Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
        Route::get('/announcements/official', [AnnouncementController::class, 'official'])->name('announcements.official');
        Route::get('/announcements/unofficial', [AnnouncementController::class, 'unofficial'])->name('announcements.unofficial');
        Route::get('/announcements/published', [AnnouncementController::class, 'published'])->name('announcements.published');
        Route::get('/announcements/drafts', [AnnouncementController::class, 'drafts'])->name('announcements.drafts');
        Route::get('/announcements/pending', [AnnouncementController::class, 'pending'])->name('announcements.pending');
        Route::get('/announcements/search', [AnnouncementController::class, 'search'])->name('announcements.search');
        Route::get('/announcements/category/{category}', [AnnouncementController::class, 'filterByCategory'])->name('announcements.category');
        Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
        Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');
        
        // Announcement POST/PUT/DELETE routes
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
        
        // Announcement action routes
        Route::post('/announcements/{announcement}/archive', [AnnouncementController::class, 'archive'])->name('announcements.archive');
        Route::post('/announcements/{announcement}/publish', [AnnouncementController::class, 'publish'])->name('announcements.publish');
        Route::post('/announcements/{announcement}/approve', [AnnouncementController::class, 'approve'])->name('announcements.approve');
        Route::post('/announcements/{announcement}/toggle-official', [AnnouncementController::class, 'toggleOfficialStatus'])->name('announcements.toggle-official');
    });

    // ============================================
    // ADMIN ROUTES - SEPARATE GROUP WITH PREFIX
    // ============================================
    
    Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Admin dashboard routes
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/recent-activity', [AdminController::class, 'getRecentActivity'])->name('recent-activity');
        Route::get('/statistics', [AdminController::class, 'getStatistics'])->name('statistics');
        
        // Admin user management routes
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminController::class, 'getUsers'])->name('index');
            Route::post('/', [AdminController::class, 'bulkAction'])->name('bulk-action');
            Route::get('/{id}', [AdminController::class, 'getUser'])->name('show');
            Route::put('/{id}', [AdminController::class, 'updateUser'])->name('update');
            Route::delete('/{id}', [AdminController::class, 'deleteUser'])->name('destroy');
            Route::patch('/{id}/toggle-verification', [AdminController::class, 'toggleUserVerification'])->name('toggle-verification');
        });
        
        // IMPORTANT: DO NOT add announcement routes here
        // Announcement routes should be outside admin prefix
    });

    // Test Route
    Route::get('/test-route/{id}', function($id) {
        return "Test route works! ID: " . $id;
    });

    // Health Check Route
    Route::get('/up', function () {
        return response()->json(['status' => 'ok']);
    });
});