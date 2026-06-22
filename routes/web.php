<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Auth\CustomRegisterController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AnnouncementQuestionController;
use App\Http\Controllers\AnnouncementReportController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CommunityHubController;
use App\Http\Controllers\Admin\FeaturedPostController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;

// Public Routes (No Auth Required)
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

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

    // Password Confirmation Routes
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
});

// Logout (allow authenticated users, including unverified, to sign out)
Route::middleware('auth')->post('/logout', [CustomLoginController::class, 'logout'])->name('logout');

// ============================================
// NOTIFICATION ROUTES (Available to ALL authenticated users)
// ============================================

Route::middleware(['auth', 'verified'])->group(function () {
    // Mark all notifications as read
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])
        ->name('notifications.markAllRead');
    
    // Mark a single notification as read (allow GET for link redirects and POST for forms)
    Route::match(['get', 'post'], '/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');
    
    // Get all notifications (JSON / paginated view)
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    // Get unread notifications count
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])
        ->name('notifications.unread-count');
});

// Authenticated Routes (Require Login)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Google Calendar Routes
    Route::get('/google-calendar/connect', [GoogleCalendarController::class, 'connect'])
        ->name('google.calendar.connect');
    Route::get('/google-calendar/callback', [GoogleCalendarController::class, 'callback'])
        ->name('google.calendar.callback');
    Route::post('/google-calendar/disconnect', [GoogleCalendarController::class, 'disconnect'])
        ->name('google.calendar.disconnect');
    Route::get('/google-calendar/status', [GoogleCalendarController::class, 'status'])
        ->name('google.calendar.status');
    Route::post('/google-calendar/sync', [GoogleCalendarController::class, 'sync'])
        ->name('google.calendar.sync');
    
    // API Routes for Events
    Route::prefix('api')->group(function () {
        Route::post('/events', [EventController::class, 'store']);
        Route::post('/events/sync-all-google', [EventController::class, 'syncAllToGoogle']);
        Route::get('/events', [EventController::class, 'index']);
        Route::get('/events/upcoming', [EventController::class, 'getUpcomingEvents']);
        Route::get('/events/statistics', [EventController::class, 'getStatistics']);
        Route::put('/events/{event}', [EventController::class, 'update']);
        Route::delete('/events/{event}', [EventController::class, 'destroy']);
        
        Route::middleware('role:admin')->group(function () {
            Route::post('/events/public', [EventController::class, 'createPublicAnnouncement']);
            Route::get('/events/public/all', [EventController::class, 'getPublicEvents']);
        });
    });

    // Logout route moved above to allow unverified users to sign out.
    
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
            case 'student':
                return redirect()->route('student.dashboard');
            default:
                \Illuminate\Support\Facades\Auth::logout();
                return redirect('/login')->withErrors(['role' => 'Invalid user role']);
        }
    })->name('dashboard');

    // ============================================
    // PROFILE ROUTES (FIXED - Proper route names)
    // ============================================
    
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Settings Routes
  Route::get('/settings', function () {
    $user = auth()->user();
    return view('settings', compact('user'));
})->name('settings');

    // ============================================
    // STUDENT ROUTES
    // ============================================
    
    Route::middleware('role:student')->group(function () {
        Route::get('/student/dashboard', function () {
            $user = auth()->user();

            $featuredAnnouncements = App\Models\Announcement::with('author')
                ->where('is_featured', 1)
                ->where('is_active', 1)
                ->where('is_banned', false)
                ->visibleOnBoard()
                ->where(function($query) {
                    $query->whereNull('published_at')
                          ->orWhere('published_at', '<=', now());
                })
                ->orderBy('featured_order', 'asc')
                ->orderBy('featured_at', 'desc')
                ->take(10)
                ->get();

            $announcements = App\Models\Announcement::with('author')
                ->where('is_active', 1)
                ->where('is_banned', false)
                ->visibleOnBoard()
                ->where(function($query) {
                    $query->whereNull('published_at')
                          ->orWhere('published_at', '<=', now());
                })
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            // Upcoming events: public events or created by the user, starting today or later
            $upcomingEvents = App\Models\Event::with(['creator', 'attendees'])
                ->whereDate('start_date', '>=', now()->toDateString())
                ->where(function ($q) use ($user) {
                    $q->where('visibility', 'public')
                      ->orWhere('user_id', $user->id);
                })
                ->orderBy('start_date')
                ->take(3)
                ->get();

            // Quick stats
            $trendingAnnouncement = App\Models\Announcement::published()->orderBy('view_count', 'desc')->first();
            $trendingTitle = $trendingAnnouncement ? $trendingAnnouncement->title : null;

            $facultyUpdatesCount = 0;
            if ($user->faculty) {
                $facultyUpdatesCount = App\Models\Announcement::where('faculty', $user->faculty)
                    ->visibleOnBoard()
                    ->whereDate('created_at', '>=', now()->subDays(7))
                    ->count();
            } else {
                // fallback: recent announcements across board
                $facultyUpdatesCount = App\Models\Announcement::visibleOnBoard()
                    ->whereDate('created_at', '>=', now()->subDays(7))
                    ->count();
            }

            $urgentCount = App\Models\Announcement::where('priority', 'urgent')
                ->visibleOnBoard()
                ->count();

            // Groups: count of groups the user is an approved member of
            $groupsCount = App\Models\GroupMember::where('user_id', $user->id)
                ->where('status', 'approved')
                ->count();

            return view('student.dashboard', compact(
                'user', 'announcements', 'featuredAnnouncements', 'upcomingEvents',
                'trendingTitle', 'facultyUpdatesCount', 'urgentCount', 'groupsCount'
            ));
        })->name('student.dashboard');
        
        Route::get('/student/calendar', function () {
            $user = auth()->user();
            return view('student.calendar', compact('user'));
        })->name('student.calendar');
        
        Route::get('/student/community-hub', [CommunityHubController::class, 'index'])->name('student.community-hub');
        Route::get('/student/community-hub/create', [CommunityHubController::class, 'create'])->name('student.community-hub.create');
        Route::post('/student/community-hub/store', [CommunityHubController::class, 'store'])->name('student.community-hub.store');
        Route::get('/student/community-hub/{id}', [CommunityHubController::class, 'show'])->name('student.community-hub.show');
        Route::put('/student/community-hub/{id}', [CommunityHubController::class, 'update'])->name('student.community-hub.update');
        Route::delete('/student/community-hub/{id}', [CommunityHubController::class, 'destroy'])->name('student.community-hub.destroy');
        Route::post('/student/community-hub/{id}/join', [CommunityHubController::class, 'join'])->name('student.community-hub.join');
        Route::post('/student/community-hub/{id}/leave', [CommunityHubController::class, 'leave'])->name('student.community-hub.leave');
        Route::put('/student/community-hub/{id}/settings', [CommunityHubController::class, 'updateSettings'])->name('student.community-hub.settings.update');
        Route::delete('/student/community-hub/{groupId}/member/{userId}', [CommunityHubController::class, 'removeMember'])->name('student.community-hub.member.remove');
        Route::post('/student/community-hub/{groupId}/join-request/{requestId}/approve', [CommunityHubController::class, 'approveJoinRequest'])->name('student.community-hub.join-request.approve');
        Route::post('/student/community-hub/{groupId}/join-request/{requestId}/reject', [CommunityHubController::class, 'rejectJoinRequest'])->name('student.community-hub.join-request.reject');
        Route::post('/student/community-hub/{groupId}/posts', [CommunityHubController::class, 'createPost'])->name('student.community-hub.post.create');
        Route::put('/student/community-hub/{groupId}/posts/{postId}', [CommunityHubController::class, 'editPost'])->name('student.community-hub.post.edit');
        Route::delete('/student/community-hub/{groupId}/posts/{postId}', [CommunityHubController::class, 'deletePost'])->name('student.community-hub.post.delete');
        Route::post('/student/community-hub/{groupId}/posts/{postId}/pin', [CommunityHubController::class, 'pinPost'])->name('student.community-hub.post.pin');
        Route::post('/student/community-hub/{groupId}/posts/{postId}/like', [CommunityHubController::class, 'likePost'])->name('student.community-hub.post.like');
        Route::post('/student/community-hub/{groupId}/posts/{postId}/comments', [CommunityHubController::class, 'createComment'])->name('student.community-hub.post.comment.create');
        Route::delete('/student/community-hub/{groupId}/posts/{postId}/comments/{commentId}', [CommunityHubController::class, 'deleteComment'])->name('student.community-hub.post.comment.delete');
        Route::post('/student/community-hub/check-group-name', [CommunityHubController::class, 'checkGroupName'])->name('student.community-hub.check-group-name');
    });

    // ============================================
    // STAFF ROUTES
    // ============================================
    
    Route::middleware('role:staff')->group(function () {
        Route::get('/staff/dashboard', function () {
            $user = auth()->user();

            $featuredAnnouncements = App\Models\Announcement::with('author')
                ->where('is_featured', 1)
                ->where('is_active', 1)
                ->where('is_banned', false)
                ->visibleOnBoard()
                ->where(function($query) {
                    $query->whereNull('published_at')
                          ->orWhere('published_at', '<=', now());
                })
                ->orderBy('featured_order', 'asc')
                ->orderBy('featured_at', 'desc')
                ->take(10)
                ->get();

            $announcements = App\Models\Announcement::with('author')
                ->where('is_active', 1)
                ->where('is_banned', false)
                ->visibleOnBoard()
                ->where(function($query) {
                    $query->whereNull('published_at')
                          ->orWhere('published_at', '<=', now());
                })
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $pendingAnnouncements = App\Models\Announcement::with('author')
                ->where('status', 'pending_verification')
                ->where('is_banned', false)
                ->latest()
                ->take(5)
                ->get();

            // Upcoming events: public events or created by the user, starting today or later
            $upcomingEvents = App\Models\Event::with(['creator', 'attendees'])
                ->whereDate('start_date', '>=', now()->toDateString())
                ->where(function ($q) use ($user) {
                    $q->where('visibility', 'public')
                      ->orWhere('user_id', $user->id);
                })
                ->orderBy('start_date')
                ->take(3)
                ->get();

            // Quick stats
            $trendingAnnouncement = App\Models\Announcement::published()->orderBy('view_count', 'desc')->first();
            $trendingTitle = $trendingAnnouncement ? $trendingAnnouncement->title : null;
            $trendingViews = $trendingAnnouncement ? $trendingAnnouncement->view_count : 0;

            $facultyUpdatesCount = 0;
            if ($user->faculty) {
                $facultyUpdatesCount = App\Models\Announcement::where('faculty', $user->faculty)
                    ->visibleOnBoard()
                    ->whereDate('created_at', '>=', now()->subDays(7))
                    ->count();
            } else {
                $facultyUpdatesCount = App\Models\Announcement::visibleOnBoard()
                    ->whereDate('created_at', '>=', now()->subDays(7))
                    ->count();
            }

            $urgentCount = App\Models\Announcement::where('priority', 'urgent')
                ->visibleOnBoard()
                ->count();

            // Staff specific activity stats
            $myAnnouncementsCount = App\Models\Announcement::where('author_id', $user->id)->count();
            $pendingReviewsCount = App\Models\Announcement::where('status', 'pending_verification')->count();
            
            $eventsThisMonthCount = App\Models\Event::whereMonth('start_date', now()->month)
                ->whereYear('start_date', now()->year)
                ->count();

            return view('staff.dashboard', compact(
                'user', 'announcements', 'featuredAnnouncements', 'upcomingEvents',
                'trendingTitle', 'trendingViews', 'facultyUpdatesCount', 'urgentCount',
                'pendingAnnouncements', 'myAnnouncementsCount', 'pendingReviewsCount', 'eventsThisMonthCount'
            ));
        })->name('staff.dashboard');
        
        Route::get('/staff/calendar', function () {
            $user = auth()->user();
            return view('staff.calendar', compact('user'));
        })->name('staff.calendar');
        
        Route::get('/staff/community-hub', [CommunityHubController::class, 'index'])->name('staff.community-hub');
        Route::get('/staff/community-hub/create', [CommunityHubController::class, 'create'])->name('staff.community-hub.create');
        Route::post('/staff/community-hub/store', [CommunityHubController::class, 'store'])->name('staff.community-hub.store');
        Route::get('/staff/community-hub/{id}', [CommunityHubController::class, 'show'])->name('staff.community-hub.show');
        Route::put('/staff/community-hub/{id}', [CommunityHubController::class, 'update'])->name('staff.community-hub.update');
        Route::delete('/staff/community-hub/{id}', [CommunityHubController::class, 'destroy'])->name('staff.community-hub.destroy');
        Route::post('/staff/community-hub/{id}/join', [CommunityHubController::class, 'join'])->name('staff.community-hub.join');
        Route::post('/staff/community-hub/{id}/leave', [CommunityHubController::class, 'leave'])->name('staff.community-hub.leave');
        Route::put('/staff/community-hub/{id}/settings', [CommunityHubController::class, 'updateSettings'])->name('staff.community-hub.settings.update');
        Route::delete('/staff/community-hub/{groupId}/member/{userId}', [CommunityHubController::class, 'removeMember'])->name('staff.community-hub.member.remove');
        Route::post('/staff/community-hub/{groupId}/join-request/{requestId}/approve', [CommunityHubController::class, 'approveJoinRequest'])->name('staff.community-hub.join-request.approve');
        Route::post('/staff/community-hub/{groupId}/join-request/{requestId}/reject', [CommunityHubController::class, 'rejectJoinRequest'])->name('staff.community-hub.join-request.reject');
        Route::post('/staff/community-hub/{groupId}/posts', [CommunityHubController::class, 'createPost'])->name('staff.community-hub.post.create');
        Route::put('/staff/community-hub/{groupId}/posts/{postId}', [CommunityHubController::class, 'editPost'])->name('staff.community-hub.post.edit');
        Route::delete('/staff/community-hub/{groupId}/posts/{postId}', [CommunityHubController::class, 'deletePost'])->name('staff.community-hub.post.delete');
        Route::post('/staff/community-hub/{groupId}/posts/{postId}/pin', [CommunityHubController::class, 'pinPost'])->name('staff.community-hub.post.pin');
        Route::post('/staff/community-hub/{groupId}/posts/{postId}/like', [CommunityHubController::class, 'likePost'])->name('staff.community-hub.post.like');
        Route::post('/staff/community-hub/{groupId}/posts/{postId}/comments', [CommunityHubController::class, 'createComment'])->name('staff.community-hub.post.comment.create');
        Route::delete('/staff/community-hub/{groupId}/posts/{postId}/comments/{commentId}', [CommunityHubController::class, 'deleteComment'])->name('staff.community-hub.post.comment.delete');
        Route::post('/staff/community-hub/check-group-name', [CommunityHubController::class, 'checkGroupName'])->name('staff.community-hub.check-group-name');
    });

    // ============================================
    // GENERAL CALENDAR ROUTE (Role-based redirect)
    // ============================================
    
    Route::get('/calendar', function () {
        $user = auth()->user();
        
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.calendar');
            case 'staff':
                return redirect()->route('staff.calendar');
            case 'student':
                return redirect()->route('student.calendar');
            default:
                return view('student.calendar', compact('user'));
        }
    })->name('calendar');

    // ============================================
    // GENERAL COMMUNITY HUB ROUTE (Auto-detect role)
    // ============================================
    
    Route::get('/community-hub', function () {
        $user = auth()->user();
        
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.community-hub');
            case 'staff':
                return redirect()->route('staff.community-hub');
            case 'student':
                return redirect()->route('student.community-hub');
            default:
                return redirect()->route('student.community-hub');
        }
    })->name('community-hub');

    Route::get('/community-hub/{id}', function ($id) {
        $user = auth()->user();
        $prefix = $user->role === 'admin' ? 'admin' : ($user->role === 'staff' ? 'staff' : 'student');
        return redirect()->route($prefix . '.community-hub.show', $id);
    })->name('community-hub.view');

    // ============================================
    // ANNOUNCEMENT ROUTES
    // ============================================
    
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
    Route::get('/announcements/published', [AnnouncementController::class, 'published'])->name('announcements.published');
    Route::get('/announcements/drafts', [AnnouncementController::class, 'drafts'])->name('announcements.drafts');
    Route::post('/announcements/{announcement}/add-to-calendar', [AnnouncementController::class, 'addToSystemCalendar'])->name('announcements.add-to-calendar');
    Route::get('/announcements/{announcement}/calendar', [AnnouncementController::class, 'downloadCalendar'])->name('announcements.calendar');
    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');
    Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::get('/my-announcements', [AnnouncementController::class, 'myAnnouncements'])->name('announcements.my-announcements');
    
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    
    Route::post('/announcements/{announcement}/archive', [AnnouncementController::class, 'archive'])->name('announcements.archive');
    Route::post('/announcements/{announcement}/publish', [AnnouncementController::class, 'publish'])->name('announcements.publish');
    Route::post('/announcements/{announcement}/toggle-official', [AnnouncementController::class, 'toggleOfficialStatus'])->name('announcements.toggle-official');
    Route::post('/announcements/{announcement}/toggle-featured', [AnnouncementController::class, 'toggleUserFeatured'])->name('announcements.toggle-featured');
    Route::post('/announcements/{announcement}/report', [AnnouncementReportController::class, 'store'])->name('announcements.report');

    // Announcement Q&A Routes
    Route::post('/announcements/{announcement}/questions', [AnnouncementQuestionController::class, 'store'])->name('announcements.questions.store');
    Route::post('/announcements/questions/{question}/answer', [AnnouncementQuestionController::class, 'answer'])->name('announcements.questions.answer');
    Route::delete('/announcements/questions/{question}', [AnnouncementQuestionController::class, 'destroy'])->name('announcements.questions.destroy');

    // Featured Announcements Route
    Route::get('/announcements/featured', [AnnouncementController::class, 'getFeatured'])->name('announcements.featured');
    // Event web routes (view + attend) - simple blade views and RSVP
    Route::get('/events-list', [EventController::class, 'listAll'])->name('events.list');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::post('/events/{event}/attend', [EventController::class, 'toggleAttend'])->name('events.attend');

    // ============================================
    // APPROVAL ROUTES (ADMIN/STAFF)
    // ============================================
    
    Route::middleware('role:admin,staff')->group(function () {
        Route::get('/announcements/{id}/details', [AnnouncementController::class, 'getDetails'])->name('announcements.details');
        Route::patch('/announcements/{id}/approve', [AnnouncementController::class, 'approve'])->name('announcements.approve');
        Route::patch('/announcements/{id}/reject', [AnnouncementController::class, 'reject'])->name('announcements.reject');
        // Verification queue removed; moderators review posts via the announcement page.
        Route::get('/announcements/rejected', [AnnouncementController::class, 'rejected'])->name('announcements.rejected');
        Route::post('/announcements/{id}/resubmit', [AnnouncementController::class, 'resubmit'])->name('announcements.resubmit');
        
        // ============================================
        // PENDING COUNT API ROUTE (For dashboard)
        // ============================================
        Route::get('/announcements/pending-count', [AnnouncementController::class, 'getPendingCount'])
            ->name('announcements.pending-count');
    });

    // ============================================
    // ADMIN ROUTES
    // ============================================

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Admin Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/recent-activity', [AdminController::class, 'getRecentActivity'])->name('recent-activity');
        Route::get('/statistics', [AdminController::class, 'getStatistics'])->name('statistics');
        Route::get('/content-stats', [AdminController::class, 'getContentStats'])->name('content-stats');
        
        // Featured Posts Management Routes
        Route::get('/featured-posts', [FeaturedPostController::class, 'index'])->name('featured-posts');
        Route::post('/featured-posts/toggle', [FeaturedPostController::class, 'toggle'])->name('featured-posts.toggle');
        Route::post('/featured-posts/reorder', [FeaturedPostController::class, 'reorder'])->name('featured-posts.reorder');
    
        // Admin Calendar
        Route::get('/calendar', function () {
            $user = auth()->user();
            return view('admin.calendar', compact('user'));
        })->name('calendar');
        
        // Admin Community Hub
        Route::get('/community-hub', [CommunityHubController::class, 'index'])->name('community-hub');
        Route::get('/community-hub/{id}', [CommunityHubController::class, 'show'])->name('community-hub.show');
        
        // Admin Moderation Page (View)
        Route::get('/moderation', [AdminController::class, 'moderation'])->name('moderation');
        
        // Admin Settings
        Route::get('/settings', function () {
            return view('admin.settings');
        })->name('settings.index');
        Route::get('/settings/data', [AdminController::class, 'getSettings'])->name('settings.data');
        Route::put('/settings/update', [AdminController::class, 'updateSettings'])->name('settings.update');
        
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
            Route::patch('/{id}/toggle-ban', [AdminController::class, 'toggleUserBan'])->name('toggle-ban');
            Route::get('/statistics', [AdminController::class, 'getUserStatistics'])->name('statistics');
        });
    });

    // ============================================
    // ADMIN MODERATION API (used by content moderation page)
    // ============================================

    Route::middleware('role:admin')->prefix('api/admin')->name('admin.api.')->group(function () {
        Route::get('/analytics', [AdminController::class, 'getAnalytics'])->name('analytics');
        Route::get('/activity', [AdminController::class, 'getActivityFeed'])->name('activity');
        Route::post('/report', [AdminController::class, 'generateReport'])->name('report');

        Route::get('/reports/statistics', [AdminController::class, 'getReportStatistics'])->name('reports.statistics');
        Route::get('/reports', [AdminController::class, 'getReports'])->name('reports.index');
        Route::get('/reports/{id}', [AdminController::class, 'getReport'])->name('reports.show');
        Route::post('/reports/{id}/dismiss', [AdminController::class, 'dismissReport'])->name('reports.dismiss');
        Route::post('/reports/{id}/ban', [AdminController::class, 'banReportedAnnouncement'])->name('reports.ban');
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


    Route::get('/test-notifications', function () {
    if (!auth()->check()) {
        return 'Please login as a student first';
    }
    $user = auth()->user();
    $notifications = $user->notifications()->take(5)->get();
    return response()->json([
        'user' => $user->name,
        'notification_count' => $user->notifications->count(),
        'unread_count' => $user->unreadNotifications->count(),
        'notifications' => $notifications
    ]);
})->middleware('auth');
    
}); // END of authenticated routes group
