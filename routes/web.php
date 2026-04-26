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
use App\Http\Controllers\EventController;

// Public Routes (No Auth Required)
Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [CustomLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [CustomLoginController::class, 'login']);
    
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
Route::middleware(['auth', 'verified'])->group(function () {
    
    // API Routes for Events
    Route::prefix('api')->group(function () {
        Route::get('/events', [EventController::class, 'index']);
        Route::post('/events', [EventController::class, 'store']);
        Route::put('/events/{event}', [EventController::class, 'update']);
        Route::delete('/events/{event}', [EventController::class, 'destroy']);
        Route::get('/events/upcoming', [EventController::class, 'getUpcomingEvents']);
        Route::get('/events/statistics', [EventController::class, 'getStatistics']);
        
        Route::middleware('role:admin')->group(function () {
            Route::post('/events/public', [EventController::class, 'createPublicAnnouncement']);
            Route::get('/events/public/all', [EventController::class, 'getPublicEvents']);
        });
    });

    // Logout
    Route::post('/logout', [CustomLoginController::class, 'logout'])->name('logout');
    
    // Dashboard Routes (Role-based)
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }
        
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
    Route::get('/profile', function () {
        $user = auth()->user();
        return view('profile', compact('user'));
    })->name('profile');
    
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/settings', function () {
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
            return view('student.calendar', compact('user'));
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

    // General Calendar Route
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
    // ANNOUNCEMENT ROUTES
    // ============================================
    
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
    Route::get('/announcements/published', [AnnouncementController::class, 'published'])->name('announcements.published');
    Route::get('/announcements/drafts', [AnnouncementController::class, 'drafts'])->name('announcements.drafts');
    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');
    Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::get('/my-announcements', [AnnouncementController::class, 'myAnnouncements'])->name('announcements.my-announcements');
    
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    
    Route::post('/announcements/{announcement}/archive', [AnnouncementController::class, 'archive'])->name('announcements.archive');
    Route::post('/announcements/{announcement}/publish', [AnnouncementController::class, 'publish'])->name('announcements.publish');
    Route::post('/announcements/{announcement}/toggle-official', [AnnouncementController::class, 'toggleOfficialStatus'])->name('announcements.toggle-official');

    // ============================================
    // APPROVAL ROUTES (ADMIN/STAFF)
    // ============================================
    
    Route::middleware('role:admin,staff')->group(function () {
        Route::patch('/announcements/{id}/approve', [AnnouncementController::class, 'approve'])->name('announcements.approve');
        Route::patch('/announcements/{id}/reject', [AnnouncementController::class, 'reject'])->name('announcements.reject');
        Route::get('/announcements/verification-queue', [AnnouncementController::class, 'verificationQueue'])->name('announcements.verification-queue');
        Route::get('/announcements/rejected', [AnnouncementController::class, 'rejected'])->name('announcements.rejected');
        Route::post('/announcements/{id}/resubmit', [AnnouncementController::class, 'resubmit'])->name('announcements.resubmit');
    });

    // ============================================
    // ADMIN ROUTES (SINGLE DEFINITION)
    // ============================================

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Admin Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/recent-activity', [AdminController::class, 'getRecentActivity'])->name('recent-activity');
        Route::get('/statistics', [AdminController::class, 'getStatistics'])->name('statistics');
        Route::get('/content-stats', [AdminController::class, 'getContentStats'])->name('content-stats');
        
        // Admin Calendar
        Route::get('/calendar', function () {
            $user = auth()->user();
            return view('admin.calendar', compact('user'));
        })->name('calendar');
        
        // Admin Moderation
        Route::get('/moderation', [AdminController::class, 'moderation'])->name('moderation');
        
        // Admin Settings
        Route::get('/settings', function () {
            return view('admin.settings');
        })->name('settings.index');
        
        // User Management Page View
        Route::view('/users', 'admin.users')->name('users');
        
        // Admin User Management API Routes
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/list', [AdminController::class, 'getUsers'])->name('index');
            Route::post('/bulk-action', [AdminController::class, 'bulkAction'])->name('bulk-action');
            Route::post('/create', [AdminController::class, 'createUser'])->name('create');
            Route::get('/{id}', [AdminController::class, 'getUser'])->name('show');
            Route::put('/{id}', [AdminController::class, 'updateUser'])->name('update');
            Route::delete('/{id}', [AdminController::class, 'deleteUser'])->name('destroy');
            Route::patch('/{id}/toggle-verification', [AdminController::class, 'toggleUserVerification'])->name('toggle-verification');
            Route::get('/statistics', [AdminController::class, 'getUserStatistics'])->name('statistics');
        });
    });

    // ============================================
    // API ROUTES
    // ============================================
    
    Route::get('/api/user/role', function () {
        return response()->json([
            'role' => auth()->user()->role,
            'id' => auth()->id(),
            'name' => auth()->user()->name
        ]);
    });

    // ============================================
    // DEBUG ROUTES (Remove in production)
    // ============================================
    
    Route::get('/debug/events', function () {
        $events = App\Models\Event::where('user_id', auth()->id())->get();
        return response()->json([
            'user' => auth()->user(),
            'events_count' => $events->count(),
            'events' => $events
        ]);
    });

    Route::get('/debug/session', function () {
        return response()->json([
            'session' => session()->all(),
            'auth' => auth()->check(),
            'user' => auth()->user()
        ]);
    });

    Route::get('/test-csrf', function () {
        return response()->json([
            'csrf_token' => csrf_token(),
            'session_token' => session()->token(),
            'has_csrf_field' => isset($_COOKIE['XSRF-TOKEN'])
        ]);
    });

    Route::get('/up', function () {
        return response()->json(['status' => 'ok']);
    });
    
}); // End of authenticated routes group