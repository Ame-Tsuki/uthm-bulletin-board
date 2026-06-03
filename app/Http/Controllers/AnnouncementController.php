<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\User;
use App\Models\AnnouncementReport;
use App\Services\LocalModService;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Notifications\AnnouncementNotification;
use Illuminate\Support\Facades\Notification; // ADD THIS MISSING IMPORT
use App\Services\ModerationService;
use App\Services\AnnouncementCalendarService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

class AnnouncementController extends Controller
{
    protected $moderationService;
    protected $calendarService;

    public function __construct(
        ModerationService $moderationService,
        AnnouncementCalendarService $calendarService
    ) {
        $this->moderationService = $moderationService;
        $this->calendarService = $calendarService;
    }

    /**
     * Display a listing of announcements.
     */
    public function index(): View
    {
        // Get authenticated user
        $user = auth()->user();
        
        // Check if is_official column exists
        $hasOfficialColumn = Schema::hasColumn('announcements', 'is_official');
        
        if ($hasOfficialColumn) {
            $query = Announcement::with('author');

            if (in_array($user->role, ['admin', 'staff'])) {
                // Active published + pending/rejected for moderation (never expired or past expiry)
                $query->listedOnMainBoard();
            } else {
                $query->visibleOnBoard();
            }

            if (Schema::hasColumn('announcements', 'is_banned')) {
                $query->notBanned();
            }
            
            $announcements = $query->latest()->paginate(10);
        } else {
            // Show all announcements if column doesn't exist
            $announcements = Announcement::with('author')->latest()->paginate(10);
        }
        
        // Return view with data
        return view('announcements.index', compact('announcements', 'user', 'hasOfficialColumn'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create(): View
    {
        $user = auth()->user();
        
        // Check if column exists to avoid errors in view
        $hasOfficialColumn = Schema::hasColumn('announcements', 'is_official');
        
        return view('announcements.create', compact('user', 'hasOfficialColumn'));
    }

    /**
 * Store a newly created announcement in storage with MODERATION.
 */
public function store(Request $request): RedirectResponse
{
    // Check if is_official column exists
    $hasOfficialColumn = Schema::hasColumn('announcements', 'is_official');
    
    // Create validation rules
    $validationRules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        'category' => 'required|in:urgent,academic,events,general,important',
        'priority' => 'nullable|in:urgent,important,normal',
        'department' => 'nullable|string|max:100',
        'publish_date' => 'nullable|date',
        'expiry_date' => [
            'nullable',
            'date',
            'after_or_equal:publish_date',
            Rule::when($request->input('status') === 'published', 'after_or_equal:today'),
        ],
        'announcement_type' => 'required|in:official,unofficial',
        'status' => 'required|in:draft,published',
    ];
    
    // Validate all fields at once
    $validated = $request->validate($validationRules);
    unset($validated['publish_date']);
    
    // ============================================
    // MODERATION CHECK - USING .NET API
    // ============================================
    $textToCheck = $validated['title'] . ' ' . $validated['content'];
    $moderationResult = $this->moderationService->moderate($textToCheck, auth()->id());
    
    // If moderation fails, block the announcement
    if (!$moderationResult['allowed']) {
        $errorMessage = "Your announcement was blocked by our content moderation system. " .
                       "Please remove inappropriate language. " .
                       "Reason: {$moderationResult['reason']}";
        
        return back()->withErrors(['moderation' => $errorMessage])->withInput();
    }
    
    // Extract toxicity score for logging
    $toxicityScore = $moderationResult['raw']['toxicityScore'] ?? 0;
    // ============================================
    // END MODERATION CHECK
    // ============================================
    
    // Add author_id to the validated data
    $validated['author_id'] = auth()->id();
    
    // Set default priority if not provided
    if (!isset($validated['priority'])) {
        $validated['priority'] = 'normal';
    }
    
    // Determine announcement type and verification status
    $announcementType = $validated['announcement_type'];
    $user = auth()->user();
    $isAdminOrStaff = in_array($user->role, ['admin', 'staff']);
    $status = $validated['status'];
    
    if ($announcementType === 'official') {
        // Official announcement
        $validated['is_official'] = true;
        
        if ($isAdminOrStaff) {
            // Admin/staff can publish official announcements immediately
            if ($status === 'published') {
                $validated['status'] = 'published';
                $validated['needs_verification'] = false;
                $validated['verified_at'] = now();
                $validated['verified_by'] = $user->id;
            } else {
                $validated['needs_verification'] = false;
            }
        } else {
            // Regular users need verification for official announcements
            if ($status === 'published') {
                $validated['status'] = 'pending_verification';
                $validated['needs_verification'] = true;
            } else {
                $validated['needs_verification'] = true;
            }
        }
    } else {
        // Unofficial announcement
        $validated['is_official'] = false;
        $validated['needs_verification'] = false;
        
        // Unofficial announcements can be published immediately by anyone
        if ($status === 'published') {
            $validated['status'] = 'published';
        }
    }
    
    // Remove announcement_type from data as it's not a database column
    unset($validated['announcement_type']);
    
    // Handle image upload
    if ($request->hasFile('image')) {
        try {
            $image = $request->file('image');
            $imagePath = $image->store('announcements', 'public');
            $validated['image'] = $imagePath;
        } catch (\Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage());
        }
    }
    
    // Save moderation results (using .NET API data)
    $validated['moderation_flagged'] = !$moderationResult['allowed'];
    $validated['moderation_results'] = json_encode([
        'toxicity_score' => $toxicityScore,
        'allowed' => $moderationResult['allowed'],
        'reason' => $moderationResult['reason'],
        'raw_response' => $moderationResult['raw'],
        'checked_at' => now()->toDateTimeString(),
        'moderation_service' => '.NET Moderation API'
    ]);
    
    // Create the announcement
    $announcement = Announcement::create($validated);
    Announcement::expireDueAnnouncements();
    
    // =========================================================================
    // NOTIFICATION LOGIC: Send notifications for published announcements
    // =========================================================================
    if ($announcement->status === 'published') {

        // Send notifications to all users except the author
        $allUsers = User::where('id', '!=', $announcement->author_id)->get();
        $title = "📢 New Announcement";
        $message = "A new announcement has been posted: '" . $announcement->title . "'";
        $url = route('announcements.show', $announcement->id);

        Notification::send($allUsers, new AnnouncementNotification($title, $message, $url, $announcement->id));

        Log::info("Notification sent for announcement #{$announcement->id}: '{$announcement->title}'");

    } elseif ($announcement->status === 'pending_verification') {

        // Only notify moderators for official announcements that need verification
        if ($announcement->is_official) {
            $moderators = User::whereIn('role', ['admin', 'staff'])->get();
            $title = "📋 Verification Required";
            $message = "A new official notice titled '{$announcement->title}' requires review.";
            $url = route('announcements.verification-queue');

            Notification::send($moderators, new AnnouncementNotification($title, $message, $url, $announcement->id));

            Log::info("Moderator notification sent for verification of official announcement #{$announcement->id}");
        }
    }
    // =========================================================================
    
    // Redirect with appropriate message
    if ($announcement->status === 'draft') {
        return redirect()->route('announcements.my-announcements', ['status' => 'draft'])
            ->with('success', 'Announcement saved as draft successfully.');
    } elseif ($announcement->status === 'pending_verification') {
        return redirect()->route('announcements.index')
            ->with('success', 'Official announcement submitted for verification. It will be published after admin/staff review.');
    } else {
        return redirect()->route('announcements.show', $announcement)
            ->with('success', 'Announcement published successfully.');
    }
}


    /**
     * Display a single announcement.
     */
    public function show($id): View
    {
        // Manually resolve the announcement since route uses {id} instead of {announcement}
        $announcement = Announcement::with('author')->find($id);

        if ($announcement) {
            $announcement->refresh();
        }
        
        if (!$announcement) {
            abort(404, 'Announcement not found');
        }
        
        // Check if user can view this announcement
        $user = auth()->user();
        if ($announcement->status === 'draft' && $announcement->author_id !== $user->id) {
            abort(403, 'Unauthorized to view this draft.');
        }
        
        if ($announcement->status === 'pending_verification' && !in_array($user->role, ['admin', 'staff'])) {
            abort(403, 'This announcement is pending verification.');
        }

        if ($announcement->status === 'expired' || $announcement->isExpired()) {
            if ($announcement->author_id !== $user->id && !in_array($user->role, ['admin', 'staff'])) {
                abort(404, 'Announcement not found');
            }
        }

        if (($announcement->is_banned ?? false) || $announcement->status === 'banned') {
            if ($announcement->author_id !== $user->id && $user->role !== 'admin') {
                abort(404, 'Announcement not found');
            }
        }
        
        // Increment view count if column exists
        if (Schema::hasColumn('announcements', 'view_count')) {
            $announcement->increment('view_count');
        }

        // =========================================================================
        // AUTOMATICALLY MARK NOTIFICATION AS READ
        // =========================================================================
        if ($user) {
            // Look through the user's unread notifications for any entry containing this announcement's ID in the stored URL
            $user->unreadNotifications()
                ->where('data->url', 'like', '%' . route('announcements.show', $announcement->id) . '%')
                ->get()
                ->markAsRead();
        }
        // =========================================================================

        $hasReported = AnnouncementReport::where('announcement_id', $announcement->id)
            ->where('reporter_id', $user->id)
            ->exists();
            
        $canReport = $announcement->author_id !== $user->id
            && !($announcement->is_banned ?? false)
            && $announcement->status !== 'banned'
            && in_array($announcement->status, ['published', 'pending_verification']);
        
        $calendar = $this->calendarService->forAnnouncement($announcement, $user->id);

        return view('announcements.show', compact('announcement', 'user', 'hasReported', 'canReport', 'calendar'));
    }

    /**
     * Add announcement to the authenticated user's system calendar.
     */
    public function addToSystemCalendar($id): JsonResponse
    {
        $announcement = Announcement::with('author')->find($id);

        if (!$announcement) {
            return response()->json(['success' => false, 'message' => 'Announcement not found.'], 404);
        }

        $this->authorizeCalendarAccess($announcement);

        $user = auth()->user();

        if ($this->calendarService->isInUserCalendar($announcement, $user->id)) {
            return response()->json([
                'success' => true,
                'already_added' => true,
                'message' => 'This announcement is already on your calendar.',
                'calendar_url' => route('calendar'),
            ]);
        }

        try {
            $this->calendarService->addToUserCalendar($announcement, $user);

            return response()->json([
                'success' => true,
                'message' => 'Added to your calendar. View it on the Calendar page.',
                'calendar_url' => route('calendar'),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to add announcement to calendar: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not add to calendar. Please try again.',
            ], 500);
        }
    }

    /**
     * Download announcement as .ics for external calendar apps.
     */
    public function downloadCalendar($id): Response
    {
        $announcement = Announcement::with('author')->find($id);

        if (!$announcement) {
            abort(404, 'Announcement not found');
        }

        $this->authorizeCalendarAccess($announcement);

        $calendar = $this->calendarService->forAnnouncement($announcement);
        $filename = 'uthm-announcement-' . $announcement->id . '.ics';

        return response($calendar['ics'], 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    protected function authorizeCalendarAccess(Announcement $announcement): void
    {
        $user = auth()->user();

        if ($announcement->status === 'draft' && $announcement->author_id !== $user->id) {
            abort(403, 'Unauthorized to view this draft.');
        }

        if ($announcement->status === 'pending_verification' && !in_array($user->role, ['admin', 'staff'])) {
            abort(403, 'This announcement is pending verification.');
        }

        if ($announcement->status === 'expired' || $announcement->isExpired()) {
            if ($announcement->author_id !== $user->id && !in_array($user->role, ['admin', 'staff'])) {
                abort(404, 'Announcement not found');
            }
        }

        if (($announcement->is_banned ?? false) || $announcement->status === 'banned') {
            if ($announcement->author_id !== $user->id && $user->role !== 'admin') {
                abort(404, 'Announcement not found');
            }
        }
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit(Announcement $announcement): View
    {
        $user = auth()->user();
        
        // Check authorization
        if ($announcement->author_id !== $user->id && !in_array($user->role, ['admin', 'staff'])) {
            abort(403, 'Unauthorized to edit this announcement.');
        }

        if ($announcement->isBanned()) {
            return redirect()->route('announcements.show', $announcement)
                ->with('error', 'This announcement has been banned and cannot be edited.');
        }
        
        // Check if column exists
        $hasOfficialColumn = Schema::hasColumn('announcements', 'is_official');
        
        return view('announcements.edit', compact('announcement', 'user', 'hasOfficialColumn'));
    }

    /**
 * Update the specified announcement in storage with MODERATION.
 */
public function update(Request $request, Announcement $announcement): RedirectResponse
{
    // Check authorization
    $user = auth()->user();
    if ($announcement->author_id !== $user->id && !in_array($user->role, ['admin', 'staff'])) {
        abort(403, 'Unauthorized to update this announcement.');
    }
    
    // Create validation rules
    $validationRules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'image' => $request->hasFile('image') ? 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120' : 'nullable',
        'category' => 'required|in:urgent,academic,events,general,important',
        'priority' => 'nullable|in:urgent,important,normal',
        'department' => 'nullable|string|max:100',
        'publish_date' => 'nullable|date',
        'expiry_date' => [
            'nullable',
            'date',
            'after_or_equal:publish_date',
            Rule::when($request->input('status') === 'published', 'after_or_equal:today'),
        ],
        'announcement_type' => 'required|in:official,unofficial',
        'status' => 'required|in:draft,published,pending_verification',
        'remove_image' => 'nullable|boolean',
    ];
    
    // Validate all fields at once
    $validated = $request->validate($validationRules);
    unset($validated['publish_date']);
    
    // ============================================
    // MODERATION CHECK - Using .NET API
    // ============================================
    $textToCheck = $validated['title'] . ' ' . $validated['content'];
    $moderationResult = $this->moderationService->moderate($textToCheck, auth()->id());
    
    // If moderation fails, block the update
    if (!$moderationResult['allowed']) {
        $errorMessage = "Your updated announcement was blocked by our content moderation system. " .
                       "Please remove inappropriate language. " .
                       "Reason: {$moderationResult['reason']}";
        
        return back()->withErrors(['moderation' => $errorMessage])->withInput();
    }
    
    // Extract toxicity score for logging
    $toxicityScore = $moderationResult['raw']['toxicityScore'] ?? 0;
    // ============================================
    // END MODERATION CHECK
    // ============================================
    
    // Determine announcement type and verification status
    $announcementType = $validated['announcement_type'];
    $isAdminOrStaff = in_array($user->role, ['admin', 'staff']);
    
    if ($announcementType === 'official') {
        // Official announcement
        $validated['is_official'] = true;
        
        if ($isAdminOrStaff) {
            // Admin/staff can publish official announcements immediately
            if ($validated['status'] === 'published') {
                $validated['status'] = 'published';
                $validated['needs_verification'] = false;
                $validated['verified_at'] = now();
                $validated['verified_by'] = $user->id;
            } elseif ($validated['status'] === 'pending_verification') {
                $validated['status'] = 'published';
                $validated['needs_verification'] = false;
                $validated['verified_at'] = now();
                $validated['verified_by'] = $user->id;
            } else {
                $validated['needs_verification'] = false;
            }
        } else {
            // Regular users need verification for official announcements
            if ($validated['status'] === 'published') {
                $validated['status'] = 'pending_verification';
                $validated['needs_verification'] = true;
                $validated['verified_at'] = null;
                $validated['verified_by'] = null;
            } elseif ($validated['status'] === 'pending_verification') {
                $validated['needs_verification'] = true;
            } else {
                $validated['needs_verification'] = true;
            }
        }
    } else {
        // Unofficial announcement
        $validated['is_official'] = false;
        $validated['needs_verification'] = false;
        $validated['verified_at'] = null;
        $validated['verified_by'] = null;
        
        // Unofficial announcements can be published immediately by anyone
        if ($validated['status'] === 'pending_verification') {
            $validated['status'] = 'published';
        }
    }
    
    // Remove announcement_type from data as it's not a database column
    unset($validated['announcement_type']);
    
    // Save moderation results (using .NET API data)
    $validated['moderation_flagged'] = !$moderationResult['allowed'];
    $validated['moderation_results'] = json_encode([
        'toxicity_score' => $toxicityScore,
        'allowed' => $moderationResult['allowed'],
        'reason' => $moderationResult['reason'],
        'raw_response' => $moderationResult['raw'],
        'checked_at' => now()->toDateTimeString(),
        'moderation_service' => '.NET Moderation API',
        'action' => 'update'
    ]);
    
    // Handle image upload
    if ($request->hasFile('image')) {
        try {
            // Delete old image if it exists
            if ($announcement->image && Storage::disk('public')->exists($announcement->image)) {
                Storage::disk('public')->delete($announcement->image);
            }
            
            $image = $request->file('image');
            $imagePath = $image->store('announcements', 'public');
            $validated['image'] = $imagePath;
        } catch (\Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage());
        }
    }
    
    // Handle image removal
    if ($request->has('remove_image') && $request->get('remove_image') == '1') {
        if ($announcement->image && Storage::disk('public')->exists($announcement->image)) {
            Storage::disk('public')->delete($announcement->image);
        }
        $validated['image'] = null;
    }
    
    // Remove remove_image from validated data
    unset($validated['remove_image']);
    
    // Update the announcement
    $announcement->update($validated);
    Announcement::expireDueAnnouncements();
    $announcement->refresh();
    
    // Redirect with appropriate message
    if ($validated['status'] === 'draft') {
        return redirect()->route('announcements.my-announcements', ['status' => 'draft'])
            ->with('success', 'Announcement updated as draft successfully.');
    } elseif ($announcementType === 'official' && isset($validated['needs_verification']) && $validated['needs_verification']) {
        return redirect()->route('announcements.my-announcements', ['status' => 'pending_verification'])
            ->with('success', 'Official announcement updated and submitted for verification.');
    } else {
        return redirect()->route('announcements.show', $announcement)
            ->with('success', 'Announcement updated successfully.');
    }
}
    /**
     * Get featured announcements for the carousel
     */
    public function getFeatured(Request $request)
    {
        try {
            $query = Announcement::with('author')
                ->where('is_featured', 1)
                ->visibleOnBoard()
                ->notBanned();
            
            // Only filter by is_active if column exists
            if (Schema::hasColumn('announcements', 'is_active')) {
                $query->where('is_active', 1);
            }
            
            // Filter by publish_date if column exists
            if (Schema::hasColumn('announcements', 'publish_date')) {
                $query->where(function($q) {
                    $q->whereNull('publish_date')
                      ->orWhere('publish_date', '<=', now());
                });
            } else {
                // Fallback for older schema
                $query->where(function($q) {
                    $q->whereNull('published_at')
                      ->orWhere('published_at', '<=', now());
                });
            }
            
            // Order by featured_order if column exists, otherwise by featured_at
            if (Schema::hasColumn('announcements', 'featured_order')) {
                $query->orderBy('featured_order', 'asc');
            }
            $query->orderBy('featured_at', 'desc');
            
            $featured = $query->take(10)->get();
            
            // Format the response
            $formatted = $featured->map(function($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'content' => $item->content,
                    'category' => $item->category,
                    'priority' => $item->priority,
                    'is_official' => $item->is_official ?? false,
                    'image_url' => $item->image_url ?? \App\Models\Announcement::DEFAULT_IMAGE_URL,
                    'created_at' => $item->created_at,
                    'author' => $item->author ? [
                        'name' => $item->author->name,
                        'role' => $item->author->role
                    ] : null
                ];
            });
            
            // If request expects JSON (AJAX call)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'announcements' => $formatted,
                    'count' => $formatted->count()
                ]);
            }
            
            // For non-AJAX, return view
            return view('announcements.featured', ['announcements' => $formatted]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching featured posts: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading featured announcements: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Unable to load featured announcements');
        }
    }

    /**
     * Remove the specified announcement from storage.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        // Check authorization
        $user = auth()->user();
        if ($announcement->author_id !== $user->id && !in_array($user->role, ['admin', 'staff'])) {
            abort(403, 'Unauthorized to delete this announcement.');
        }
        
        // Delete the announcement
        $announcement->delete();

        // Redirect to user's announcements
        return redirect()->route('announcements.my-announcements')
            ->with('success', 'Announcement deleted successfully.');
    }

    /**
     * Display user's own announcements
     */
    public function myAnnouncements(Request $request): View
    {
        $user = auth()->user();
        $status = $request->get('status', 'all');
        
        // Get counts from database BEFORE applying status filter
        $totalCount = Announcement::where('author_id', $user->id)->count();
        $publishedCount = Announcement::where('author_id', $user->id)->where('status', 'published')->count();
        $expiredCount = Announcement::where('author_id', $user->id)->where('status', 'expired')->count();
        $draftCount = Announcement::where('author_id', $user->id)->where('status', 'draft')->count();
        $pendingCount = Announcement::where('author_id', $user->id)->where('status', 'pending_verification')->count();
        $rejectedCount = Announcement::where('author_id', $user->id)->where('status', 'rejected')->count();
        $bannedCount = Announcement::where('author_id', $user->id)->where('status', 'banned')->count();
        
        // Then apply status filter for the paginated results
        $query = Announcement::where('author_id', $user->id);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $announcements = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Calculate total views
        $totalViews = 0;
        if (Schema::hasColumn('announcements', 'view_count')) {
            $totalViews = Announcement::where('author_id', $user->id)->sum('view_count');
        }
        
        return view('announcements.my-announcements', compact(
            'announcements', 
            'totalViews', 
            'user',
            'totalCount',
            'publishedCount',
            'expiredCount',
            'draftCount',
            'pendingCount',
            'rejectedCount',
            'bannedCount'
        ));
    }

    /**
 * APPROVE announcement (Admin & Staff)
 * Convert from pending_verification to published
 */
public function approve(Request $request, $id)
{
    try {
        // Add debug logging
        Log::info('Approve method called for ID: ' . $id);
        Log::info('Request method: ' . $request->method());
        Log::info('User: ' . auth()->user()->id . ', Role: ' . auth()->user()->role);
        
        $announcement = Announcement::findOrFail($id);
        $user = auth()->user();
        
        // Check authorization
        if (!in_array($user->role, ['admin', 'staff'])) {
            Log::warning('Unauthorized user tried to approve: ' . $user->role);
            return response()->json(['success' => false, 'message' => 'Unauthorized. Only admin/staff can approve announcements.'], 403);
        }
        
        // Check if announcement is pending verification
        if ($announcement->status !== 'pending_verification') {
            Log::warning('Announcement not pending: ' . $announcement->status);
            return response()->json([
                'success' => false, 
                'message' => 'Only pending announcements can be approved. Current status: ' . $announcement->status
            ], 400);
        }
        
        // Update announcement
        $announcement->update([
            'status' => 'published',
            'is_official' => true,
            'needs_verification' => false,
            'verified_at' => now(),
            'verified_by' => $user->id,
            'rejection_reason' => null,
            'rejected_at' => null,
            'rejected_by' => null,
        ]);
        
        Log::info('Announcement approved successfully: ' . $announcement->id);
        
        // ============================================
        // SEND NOTIFICATION TO ALL USERS AFTER APPROVAL
        // ============================================
        $allUsers = \App\Models\User::where('id', '!=', $announcement->author_id)->get();
        $title = "📢 New Official Announcement";
        $message = "A new official announcement has been posted: '" . $announcement->title . "'";
        $url = route('announcements.show', $announcement->id);
        
        \Illuminate\Support\Facades\Notification::send($allUsers, new \App\Notifications\AnnouncementNotification($title, $message, $url, $announcement->id));
        
        Log::info("Approval notification sent to " . $allUsers->count() . " users for announcement #{$announcement->id}");
        // ============================================
        
        return response()->json([
            'success' => true, 
            'message' => 'Announcement "' . $announcement->title . '" has been approved and published.',
            'data' => $announcement
        ]);
        
    } catch (\Exception $e) {
        Log::error('Approval failed: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        return response()->json([
            'success' => false, 
            'message' => 'Error approving announcement: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * REJECT announcement (Admin & Staff)
     * Convert from pending_verification to rejected with reason
     */
    public function reject(Request $request, $id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $user = auth()->user();
            
            // Check authorization - only admin and staff can reject
            if (!in_array($user->role, ['admin', 'staff'])) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized. Only admin/staff can reject announcements.'], 403);
                }
                abort(403, 'Unauthorized. Only admin/staff can reject announcements.');
            }
            
            // Validate rejection reason
            $validator = validator($request->all(), [
                'reason' => 'required|string|min:3|max:500',
            ]);
            
            if ($validator->fails()) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Rejection reason is required', 'errors' => $validator->errors()], 422);
                }
                return redirect()->back()->with('error', 'Please provide a reason for rejection.');
            }
            
            $reason = $request->input('reason');
            
            // Check if announcement is pending verification
            if ($announcement->status !== 'pending_verification') {
                $message = 'Only pending announcements can be rejected. Current status: ' . $announcement->status;
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 400);
                }
                return redirect()->back()->with('error', $message);
            }
            
            // Update announcement
            $announcement->update([
                'status' => 'rejected',
                'needs_verification' => false,
                'rejection_reason' => $reason,
                'rejected_at' => now(),
                'rejected_by' => $user->id,
                'verified_at' => null,
                'verified_by' => null,
            ]);
            
            $message = 'Announcement "' . $announcement->title . '" has been rejected.';
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => $message,
                    'data' => $announcement
                ]);
            }
            
            // Redirect back to verification queue
            return redirect()->route('announcements.verification-queue')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            Log::error('Rejection failed: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error rejecting announcement: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error rejecting announcement: ' . $e->getMessage());
        }
    }

    /**
     * Show verification queue for admin/staff
     */
    public function verificationQueue(Request $request): View
    {
        $user = auth()->user();
        
        // Only admin/staff can see verification queue
        if (!in_array($user->role, ['admin', 'staff'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $query = Announcement::with('author')
            ->where('status', 'pending_verification')
            ->orderBy('created_at', 'asc');
        
        // Optional filtering
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        $announcements = $query->paginate(10);
        
        return view('announcements.verification-queue', compact('announcements', 'user'));
    }

    /**
     * Get pending count for API
     */
    public function getPendingCount()
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'staff'])) {
            return response()->json(['count' => 0]);
        }
        
        $count = Announcement::where('status', 'pending_verification')->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Archive the specified announcement.
     */
    public function archive(Announcement $announcement): RedirectResponse
    {
        $announcement->update(['status' => 'archived']);
        
        return redirect()->back()->with('success', 'Announcement archived successfully.');
    }

    /**
     * Publish the specified announcement.
     */
    public function publish(Announcement $announcement): RedirectResponse
    {
        // Check if announcement needs verification
        if (($announcement->is_official ?? false) && ($announcement->needs_verification ?? false)) {
            $announcement->update(['status' => 'pending_verification']);
            
            return redirect()->route('announcements.my-announcements')
                ->with('info', 'Official announcement submitted for verification.');
        } else {
            $announcement->update(['status' => 'published']);
            
            return redirect()->route('announcements.my-announcements')
                ->with('success', 'Announcement published successfully.');
        }
    }

    /**
     * Show only published announcements.
     */
    public function published(): View
    {
        $user = auth()->user();
        $announcements = Announcement::visibleOnBoard()
            ->notBanned()
            ->latest()
            ->paginate(10);
            
        $hasOfficialColumn = Schema::hasColumn('announcements', 'is_official');
        
        return view('announcements.published', compact('announcements', 'user', 'hasOfficialColumn'));
    }

    /**
     * Show only draft announcements.
     */
    public function drafts(): View
    {
        $user = auth()->user();
        $announcements = Announcement::where('status', 'draft')
            ->latest()
            ->paginate(10);
            
        $hasOfficialColumn = Schema::hasColumn('announcements', 'is_official');
        
        return view('announcements.drafts', compact('announcements', 'user', 'hasOfficialColumn'));
    }

    /**
     * Toggle official status of an announcement.
     */
    public function toggleOfficialStatus(Announcement $announcement): RedirectResponse
    {
        // Only admin can toggle official status
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        if (!Schema::hasColumn('announcements', 'is_official')) {
            return redirect()->back()->with('error', 'Database column not found. Please run migration first.');
        }
        
        try {
            $announcement->update([
                'is_official' => !$announcement->is_official,
                'needs_verification' => false,
                'verified_at' => $announcement->is_official ? null : now(),
                'verified_by' => $announcement->is_official ? null : auth()->id(),
            ]);
            
            $action = $announcement->is_official ? 'marked as official' : 'marked as unofficial';
            
            return redirect()->back()->with('success', "Announcement {$action} successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update announcement status: ' . $e->getMessage());
        }
    }

    /**
     * Toggle featured status for user announcement (author or admin only).
     */
    public function toggleUserFeatured(Announcement $announcement): RedirectResponse
    {
        $user = auth()->user();
        
        // Only author or admin can toggle featured status
        if ($announcement->author_id !== $user->id && $user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        // Only staff and club_admin can feature their own announcements
        if ($user->role !== 'admin' && !in_array($user->role, ['staff', 'club_admin'])) {
            abort(403, 'Only staff and club admins can feature their announcements.');
        }
        
        if (!Schema::hasColumn('announcements', 'is_featured')) {
            return redirect()->back()->with('error', 'Database column not found. Please run migration first.');
        }
        
        try {
            // Check if announcement is published
            if ($announcement->status !== 'published') {
                return redirect()->back()->with('error', 'Only published announcements can be featured.');
            }
            
            // Toggle featured status
            $is_featured = !$announcement->is_featured;
            
            $announcement->update([
                'is_featured' => $is_featured,
                'featured_at' => $is_featured ? now() : null,
            ]);
            
            $action = $is_featured ? 'added to featured' : 'removed from featured';
            
            return redirect()->back()->with('success', "Announcement {$action} successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update announcement: ' . $e->getMessage());
        }
    }
}
