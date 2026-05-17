<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnnouncementReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AdminController extends Controller
{
   /**
     * Display admin dashboard with statistics
     */
    public function dashboard()
    {
        // 1. Get base counts first so we can do math on them
        $totalUsers = \App\Models\User::count();
        $students = \App\Models\User::where('role', 'student')->count();
        $staff = \App\Models\User::where('role', 'staff')->count();
        $pendingReports = \App\Models\AnnouncementReport::where('status', 'pending')->count();

        // 2. Calculate Role Percentages (safely avoiding division by zero)
        $studentPercentage = $totalUsers > 0 ? round(($students / $totalUsers) * 100, 1) : 0;
        $staffPercentage = $totalUsers > 0 ? round(($staff / $totalUsers) * 100, 1) : 0;

        // 3. Calculate User Growth Percentage (This Month vs Last Month)
        $currentMonthNewUsers = \App\Models\User::whereMonth('created_at', \Carbon\Carbon::now()->month)
                                    ->whereYear('created_at', \Carbon\Carbon::now()->year)
                                    ->count();

        $lastMonthNewUsers = \App\Models\User::whereMonth('created_at', \Carbon\Carbon::now()->subMonth()->month)
                                  ->whereYear('created_at', \Carbon\Carbon::now()->subMonth()->year)
                                  ->count();

        if ($lastMonthNewUsers > 0) {
            $growthPercentage = round((($currentMonthNewUsers - $lastMonthNewUsers) / $lastMonthNewUsers) * 100, 1);
        } else {
            $growthPercentage = $currentMonthNewUsers > 0 ? 100 : 0; 
        }

        // 4. Build the final stats array
        $stats = [
            // Base Stats
            'total_users' => $totalUsers,
            'students' => $students,
            'staff' => $staff,
            
            // The Calculated Percentages for the View
            'user_growth_percentage' => $growthPercentage,
            'student_percentage' => $studentPercentage,
            'staff_percentage' => $staffPercentage,
            
            // Other User Stats
            'verified_users' => \App\Models\User::where('is_verified', true)->count(),
            'unverified_users' => \App\Models\User::where('is_verified', false)->count(),
            'recent_users' => \App\Models\User::orderBy('created_at', 'desc')->take(10)->get(),
            
            // Reports & Announcements
            'pending_reports' => $pendingReports,
            'pending_verification_text' => $pendingReports > 0 ? 'Requires review' : 'All clear',
            'pending_verification_announcements' => \App\Models\Announcement::where('status', 'pending_verification')->count(),
            
            // System
            'system_status' => [
                ['name' => 'Database', 'status' => 'online', 'value' => 'Online'],
                ['name' => 'Mail Server', 'status' => 'online', 'value' => 'Online'],
                ['name' => 'Storage', 'status' => 'warning', 'value' => '75% Used'],
                ['name' => 'API Services', 'status' => 'online', 'value' => 'Online'],
            ],
            'recent_activities' => $this->getRecentActivityData(),
        ];

        return view('admin.admin', compact('stats'));
    }


    public function markAllNotificationsRead()
{
    auth()->user()->unreadNotifications->markAsRead();
    return back()->with('success', 'All notifications marked as read.');
}


    /**
     * Get recent activity data for dashboard
     */
    private function getRecentActivityData()
    {
        $activities = [];
        
        // Get recent user registrations
        $recentUsers = \App\Models\User::orderBy('created_at', 'desc')->take(3)->get();
        foreach ($recentUsers as $user) {
            $activities[] = [
                'icon_bg' => 'bg-blue-100',
                'icon' => 'fas fa-user-plus',
                'icon_color' => 'text-blue-600',
                'message' => "New user registered: {$user->name}",
                'time_ago' => $user->created_at->diffForHumans(),
            ];
        }
        
        // Get recent announcements
        $recentAnnouncements = \App\Models\Announcement::orderBy('created_at', 'desc')->take(2)->get();
        foreach ($recentAnnouncements as $announcement) {
            $activities[] = [
                'icon_bg' => 'bg-green-100',
                'icon' => 'fas fa-megaphone',
                'icon_color' => 'text-green-600',
                'message' => "New announcement: {$announcement->title}",
                'time_ago' => $announcement->created_at->diffForHumans(),
            ];
        }
        
        return $activities;
    }

    /**
     * Show moderation page with hydrated data queues
     */
    public function moderation()
    {
        $user = auth()->user();

        // 1. Fetch announcements waiting for initial verification
        $pendingAnnouncements = \App\Models\Announcement::with('author')
            ->where('status', 'pending_verification')
            ->latest()
            ->get();

        // 2. Fetch pending user reports 
        $pendingReports = AnnouncementReport::with(['announcement.author', 'reporter'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        // 3. Fetch current status summary counters
        $reportStats = [
            'pending'   => AnnouncementReport::where('status', 'pending')->count(),
            'resolved'  => AnnouncementReport::where('status', 'resolved')->count(),
            'dismissed' => AnnouncementReport::where('status', 'dismissed')->count(),
            'total'     => AnnouncementReport::count(),
        ];

        return view('admin.moderation', compact('user', 'pendingAnnouncements', 'pendingReports', 'reportStats'));
    }

    /**
     * Get content statistics
     */
    public function getContentStats()
    {
        $stats = [
            'total_announcements' => \App\Models\Announcement::count(),
            'published_announcements' => \App\Models\Announcement::where('status', 'published')->count(),
            'pending_announcements' => \App\Models\Announcement::where('status', 'pending_verification')->count(), // FIXED: Changed 'pending' to 'pending_verification'
            'rejected_announcements' => \App\Models\Announcement::where('status', 'rejected')->count(),
            'draft_announcements' => \App\Models\Announcement::where('status', 'draft')->count(),
            'total_events' => \App\Models\Event::count(),
            'upcoming_events' => \App\Models\Event::where('event_date', '>=', now())->count(),
            'past_events' => \App\Models\Event::where('event_date', '<', now())->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    // ============================================
    // MODERATION API METHODS
    // ============================================

    public function getReportStatistics()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'pending' => AnnouncementReport::where('status', 'pending')->count(),
                'resolved' => AnnouncementReport::where('status', 'resolved')->count(),
                'dismissed' => AnnouncementReport::where('status', 'dismissed')->count(),
                'total' => AnnouncementReport::count(),
            ],
        ]);
    }

    public function getReports(Request $request)
    {
        $status = $request->get('status', 'all');
        $priority = $request->get('priority', 'all');
        $search = $request->get('search', '');

        $query = AnnouncementReport::with(['announcement.author', 'reporter'])
            ->latest();

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($priority && $priority !== 'all') {
            $query->where('priority', $priority);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                    ->orWhereHas('reporter', fn ($r) => $r->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('announcement', fn ($a) => $a->where('title', 'like', "%{$search}%"));
            });
        }

        $reports = $query->get()->map(fn ($report) => $report->toModerationArray());

        return response()->json([
            'success' => true,
            'data' => $reports,
        ]);
    }

    public function getReport($id)
    {
        $report = AnnouncementReport::with(['announcement.author', 'reporter', 'resolver'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report->toModerationArray(),
        ]);
    }

    public function dismissReport(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $report = AnnouncementReport::findOrFail($id);

        if ($report->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This report has already been processed.',
            ], 422);
        }

        $report->update([
            'status' => 'dismissed',
            'resolution_note' => $validated['reason'] ?? 'Report dismissed by admin.',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report dismissed. The announcement was not changed.',
        ]);
    }

    public function banReportedAnnouncement(Request $request, $id)
{
    $validated = $request->validate([
        'reason' => 'nullable|string|max:500',
    ]);

    $report = AnnouncementReport::with('announcement')->findOrFail($id);

    if ($report->status !== 'pending') {
        return response()->json([
            'success' => false,
            'message' => 'This report has already been processed.',
        ], 422);
    }

    $announcement = $report->announcement;

    if (! $announcement) {
        return response()->json([
            'success' => false,
            'message' => 'Announcement not found.',
        ], 404);
    }

    $banReason = $validated['reason'] ?? 'Removed due to community reports.';

    DB::transaction(function () use ($announcement, $report, $banReason) {
        // Your existing ban logic
        $announcement->status = 'banned';
        $announcement->is_banned = true;
        $announcement->is_active = false;
        $announcement->banned_at = now();
        $announcement->banned_by = auth()->id();
        $announcement->ban_reason = $banReason;
        
        // ADD THIS BLOCK to automatically remove featured status upon ban
        if ($announcement->is_featured) {
            $announcement->is_featured = false;
            $announcement->featured_order = null;
            $announcement->featured_at = null;
            
            // Reorder the remaining featured posts so there are no gaps
            $featured = \App\Models\Announcement::where('is_featured', true)
                ->where('status', 'published') // Only reorder published featured posts
                ->orderBy('featured_order')
                ->get();
                
            foreach ($featured as $index => $item) {
                $item->featured_order = $index + 1;
                $item->save();
            }
        }
        
        $announcement->save();

        // Update all pending reports for this announcement
        AnnouncementReport::where('announcement_id', $announcement->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'resolved',
                'resolution_note' => $banReason,
                'resolved_by' => auth()->id(),
                'resolved_at' => now(),
            ]);
    });

    return response()->json([
        'success' => true,
        'message' => 'Announcement has been banned and hidden from users.',
    ]);
}

    // ============================================
    // USER MANAGEMENT METHODS
    // ============================================

    /**
     * Get all users with pagination
     */
    public function getUsers(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search', '');
        $role = $request->get('role', '');
        $verified = $request->get('verified', '');
        $banned = $request->get('banned', '');

        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('uthm_id', 'like', "%{$search}%");
            });
        }

        if ($role) {
            $query->where('role', $role);
        }

        if ($verified !== '') {
            $query->where('is_verified', $verified === 'true' || $verified === '1');
        }

        if ($banned !== '') {
            $query->where('is_banned', $banned === 'true' || $banned === '1');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Get single user details
     */
    public function getUser($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Create a new user
     */
    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'uthm_id' => 'nullable|string|unique:users,uthm_id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => ['required', Rule::in(['student', 'staff', 'admin', 'club_admin'])],
            'faculty' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
            'is_verified' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_verified'] = $validated['is_verified'] ?? false;

        if (empty($validated['uthm_id'])) {
            $validated['uthm_id'] = 'ADM-' . strtoupper(uniqid());
        }

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Update user information
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'uthm_id' => ['sometimes', 'string', Rule::unique('users')->ignore($user->id)],
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'string', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role' => ['sometimes', Rule::in(['student', 'staff', 'admin', 'club_admin'])],
            'faculty' => 'nullable|string|max:255',
            'password' => 'sometimes|string|min:8',
            'is_verified' => 'boolean',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user->fresh()
        ]);
    }

    /**
     * Delete a user
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting yourself
        if ($user->id == optional(auth()->guard('web')->user())->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account'
            ], 403);
        }
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Ban or unban a user
     */
    public function toggleUserBan($id)
    {
        $user = User::findOrFail($id);

        if ($user->id == optional(auth()->guard('web')->user())->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot ban your own account',
            ], 403);
        }

        $user->is_banned = !$user->is_banned;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => $user->is_banned ? 'User banned successfully' : 'User unbanned successfully',
            'data' => $user,
        ]);
    }

    /**
     * Verify/Unverify a user
     */
    public function toggleUserVerification($id)
    {
        $user = User::findOrFail($id);
        $user->is_verified = !$user->is_verified;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => $user->is_verified ? 'User verified successfully' : 'User unverified successfully',
            'data' => $user
        ]);
    }

    /**
     * Bulk operations on users
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', Rule::in(['verify', 'unverify', 'ban', 'unban', 'delete', 'change_role'])],
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role' => 'required_if:action,change_role|string',
        ]);

        $userIds = $validated['user_ids'];
        $action = $validated['action'];

        $currentUserId = optional(auth()->guard('web')->user())->id;

        if (in_array($currentUserId, $userIds) && in_array($action, ['delete', 'ban'])) {
            return response()->json([
                'success' => false,
                'message' => $action === 'ban'
                    ? 'You cannot ban your own account'
                    : 'You cannot delete your own account',
            ], 403);
        }

        $users = User::whereIn('id', $userIds);

        switch ($action) {
            case 'verify':
                $users->update(['is_verified' => true]);
                $message = 'Users verified successfully';
                break;
            case 'unverify':
                $users->update(['is_verified' => false]);
                $message = 'Users unverified successfully';
                break;
            case 'ban':
                $users->where('id', '!=', $currentUserId)->update(['is_banned' => true]);
                $message = 'Users banned successfully';
                break;
            case 'unban':
                $users->update(['is_banned' => false]);
                $message = 'Users unbanned successfully';
                break;
            case 'delete':
                $users->delete();
                $message = 'Users deleted successfully';
                break;
            case 'change_role':
                $users->update(['role' => $validated['role']]);
                $message = 'User roles updated successfully';
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'affected_count' => count($userIds)
        ]);
    }

    /**
     * Get user statistics by role
     */
    public function getUserStatistics()
    {
        $stats = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->get()
            ->keyBy('role');

        $verifiedStats = [
            'verified' => User::where('is_verified', true)->count(),
            'unverified' => User::where('is_verified', false)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'by_role' => $stats,
                'by_verification' => $verifiedStats,
            ]
        ]);
    }

    /**
     * Get recent activity (recent users)
     */
    public function getRecentActivity()
    {
        $recentUsers = User::orderBy('created_at', 'desc')->take(20)->get([
            'id', 'name', 'email', 'role', 'is_verified', 'created_at'
        ]);

        return response()->json([
            'success' => true,
            'data' => $recentUsers
        ]);
    }

    /**
     * Get analytics data (announcements, events, engagement)
     */
    public function getAnalytics()
    {
        $announcements = \App\Models\Announcement::count();
        $events = \App\Models\Event::count();
        $totalViews = \App\Models\Announcement::sum('view_count') ?? 0;
        
        $stats = [
            'total_announcements' => $announcements,
            'total_events' => $events,
            'total_views' => $totalViews,
            'avg_views_per_announcement' => $announcements > 0 ? round($totalViews / $announcements, 2) : 0,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get announcements for moderation
     */
    public function getAnnouncements(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $status = $request->get('status', '');
        $search = $request->get('search', '');

        $query = \App\Models\Announcement::query()->with('author');

        // Exclude draft announcements for admin view (drafts are personal)
        $query->whereIn('status', ['pending_verification', 'published', 'rejected']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $announcements = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $announcements
        ]);
    }

    /**
     * Approve an announcement
     */
    public function approveAnnouncement($id)
    {
        $announcement = \App\Models\Announcement::findOrFail($id);
        $announcement->status = 'published';
        $announcement->save();

        return response()->json([
            'success' => true,
            'message' => 'Announcement approved successfully',
            'data' => $announcement
        ]);
    }

    /**
     * Reject an announcement
     */
    public function rejectAnnouncement(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $announcement = \App\Models\Announcement::findOrFail($id);
        $announcement->status = 'rejected';
        $announcement->rejection_reason = $validated['reason'];
        $announcement->save();

        return response()->json([
            'success' => true,
            'message' => 'Announcement rejected successfully',
            'data' => $announcement
        ]);
    }

    /**
     * Get a single announcement
     */
    public function getAnnouncement($id)
    {
        $announcement = \App\Models\Announcement::with('author')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $announcement
        ]);
    }

    /**
     * Store a new announcement
     */
    public function storeAnnouncement(Request $request)
    {
        try {
            \Log::info('Store announcement request:', $request->all());
            
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category' => 'required|string',
                'status' => 'required|string|in:draft,pending_verification,published,rejected',
                'is_official' => 'nullable|boolean'
            ]);

            $announcement = \App\Models\Announcement::create([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'category' => $validated['category'],
                'status' => $validated['status'],
                'is_official' => $validated['is_official'] ?? false,
                'author_id' => auth()->id() ?? 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Announcement created successfully',
                'data' => $announcement
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Store announcement error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an announcement
     */
    public function updateAnnouncement(Request $request, $id)
    {
        try {
            \Log::info('Update announcement request:', ['id' => $id, 'data' => $request->all()]);
            
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category' => 'required|string',
                'status' => 'required|string|in:draft,pending_verification,published,rejected',
                'is_official' => 'nullable|boolean'
            ]);

            $announcement = \App\Models\Announcement::findOrFail($id);
            $announcement->update([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'category' => $validated['category'],
                'status' => $validated['status'],
                'is_official' => $validated['is_official'] ?? false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Announcement updated successfully',
                'data' => $announcement
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Update announcement error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an announcement
     */
    public function deleteAnnouncement($id)
    {
        $announcement = \App\Models\Announcement::findOrFail($id);
        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted successfully'
        ]);
    }

    /**
     * Delete an event
     */
    public function deleteEvent($id)
    {
        $event = \App\Models\Event::findOrFail($id);
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
    }

    /**
     * Get system settings
     */
    public function getSettings()
    {
        $settings = [];
        
        // Check if settings table exists before querying
        if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            $settings = \App\Models\Setting::all()->keyBy('key')->map(function ($item) {
                return $item->value;
            })->toArray();
        }

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Update system settings
     */
    public function updateSettings(Request $request)
    {
        // Check if settings table exists before updating
        if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            return response()->json([
                'success' => false,
                'message' => 'Settings table not found. Please run migrations.'
            ], 500);
        }

        $settings = $request->all();

        foreach ($settings as $key => $value) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    }

    /**
     * Generate dashboard report
     */
    public function generateReport(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year

        $date = match ($period) {
            'day' => now()->subDay(),
            'week' => now()->subWeek(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };

        $report = [
            'period' => $period,
            'new_users' => User::where('created_at', '>=', $date)->count(),
            'new_announcements' => \App\Models\Announcement::where('created_at', '>=', $date)->count(),
            'new_events' => \App\Models\Event::where('created_at', '>=', $date)->count(),
            'verified_users' => User::where('is_verified', true)->where('created_at', '>=', $date)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get featured announcements for admin management
     */
    public function getFeaturedAnnouncements(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search', '');

        $query = \App\Models\Announcement::where('is_featured', true)
            ->where('status', 'published')
            ->with('author');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $announcements = $query->orderBy('featured_order', 'asc')
            ->orderBy('featured_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $announcements
        ]);
    }

    /**
     * Get all announcements available for featuring
     */
    public function getAvailableAnnouncements(Request $request)
    {
        $search = $request->get('search', '');
        $limit = $request->get('limit', 10);

        $query = \App\Models\Announcement::where('status', 'published')
            ->where('is_featured', false)
            ->with('author');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $announcements = $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $announcements
        ]);
    }

    /**
     * Toggle featured status for an announcement
     */
    public function toggleFeaturedAnnouncement($id)
    {
        $announcement = \App\Models\Announcement::findOrFail($id);

        if ($announcement->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Only published announcements can be featured'
            ], 422);
        }

        if ($announcement->is_featured) {
            // Remove from featured
            $announcement->is_featured = false;
            $announcement->featured_at = null;
            $announcement->featured_order = null;
            $announcement->save();

            return response()->json([
                'success' => true,
                'message' => 'Announcement removed from featured',
                'data' => $announcement
            ]);
        } else {
            // Add to featured
            $maxOrder = \App\Models\Announcement::where('is_featured', true)
                ->max('featured_order') ?? 0;

            $announcement->is_featured = true;
            $announcement->featured_at = now();
            $announcement->featured_order = $maxOrder + 1;
            $announcement->save();

            return response()->json([
                'success' => true,
                'message' => 'Announcement added to featured',
                'data' => $announcement
            ]);
        }
    }

    /**
     * Update featured order
     */
    public function updateFeaturedOrder(Request $request)
    {
        $validated = $request->validate([
            'announcements' => 'required|array',
            'announcements.*.id' => 'required|exists:announcements,id',
            'announcements.*.order' => 'required|integer|min:0'
        ]);

        foreach ($validated['announcements'] as $item) {
            \App\Models\Announcement::where('id', $item['id'])
                ->update(['featured_order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Featured announcements order updated successfully'
        ]);
    }

    /**
     * Add multiple announcements to featured
     */
    public function addToFeatured(Request $request)
    {
        $validated = $request->validate([
            'announcement_ids' => 'required|array',
            'announcement_ids.*' => 'exists:announcements,id'
        ]);

        $maxOrder = \App\Models\Announcement::where('is_featured', true)
            ->max('featured_order') ?? 0;

        $announcements = \App\Models\Announcement::whereIn('id', $validated['announcement_ids'])
            ->where('status', 'published')
            ->where('is_featured', false)
            ->get();

        foreach ($announcements as $index => $announcement) {
            $announcement->is_featured = true;
            $announcement->featured_at = now();
            $announcement->featured_order = $maxOrder + $index + 1;
            $announcement->save();
        }

        return response()->json([
            'success' => true,
            'message' => count($announcements) . ' announcements added to featured',
            'data' => $announcements
        ]);
    }

    /**
     * Remove multiple announcements from featured
     */
    public function removeFromFeatured(Request $request)
    {
        $validated = $request->validate([
            'announcement_ids' => 'required|array',
            'announcement_ids.*' => 'exists:announcements,id'
        ]);

        \App\Models\Announcement::whereIn('id', $validated['announcement_ids'])
            ->update([
                'is_featured' => false,
                'featured_at' => null,
                'featured_order' => null
            ]);

        return response()->json([
            'success' => true,
            'message' => count($validated['announcement_ids']) . ' announcements removed from featured'
        ]);
    }
}