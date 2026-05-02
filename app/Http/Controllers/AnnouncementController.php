<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\User;
use App\Services\LocalModService;  // ADD THIS
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    protected $localMod;  // ADD THIS

    /**
     * Constructor with LocalMod injection
     */
    public function __construct(LocalModService $localMod)  // ADD THIS
    {
        $this->localMod = $localMod;
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
            // Show mixed announcements (both official and unofficial)
            // For non-admin/staff, show all published announcements
            // For admin/staff, also show pending_verification announcements
            $query = Announcement::with('author');
            
            if (in_array($user->role, ['admin', 'staff'])) {
                // Admin/staff can see all announcements except drafts
                $query->where('status', '!=', 'draft');
            } else {
                // Regular users see only published announcements
                $query->where('status', 'published');
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
            'expiry_date' => 'nullable|date|after_or_equal:publish_date',
            'announcement_type' => 'required|in:official,unofficial',
            'status' => 'required|in:draft,published',
        ];
        
        // Validate all fields at once
        $validated = $request->validate($validationRules);
        
        // ============================================
        // MODERATION CHECK - Check title and content
        // ============================================
        $titleModeration = $this->localMod->analyzeText($validated['title'], ['toxicity', 'pii', 'spam']);
        $contentModeration = $this->localMod->analyzeText($validated['content'], ['toxicity', 'pii', 'spam']);
        
        // If moderation fails, block the announcement
        if (($titleModeration['flagged'] ?? false) || ($contentModeration['flagged'] ?? false)) {
            $violations = [];
            
            if ($titleModeration['flagged'] ?? false) {
                foreach ($titleModeration['results'] ?? [] as $v) {
                    if ($v['flagged'] ?? false) {
                        $violations[] = "Title contains {$v['classifier']}";
                    }
                }
            }
            
            if ($contentModeration['flagged'] ?? false) {
                foreach ($contentModeration['results'] ?? [] as $v) {
                    if ($v['flagged'] ?? false) {
                        $violations[] = "Content contains {$v['classifier']}";
                    }
                }
            }
            
            $violationText = implode(', ', $violations);
            $errorMessage = "Your announcement was blocked by our content moderation system. " .
                           "Please remove inappropriate language. " .
                           "Detected: {$violationText}";
            
            return back()->withErrors(['moderation' => $errorMessage])->withInput();
        }
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
        
        // Save moderation results (optional - for audit)
        $validated['moderation_flagged'] = ($titleModeration['flagged'] ?? false) || ($contentModeration['flagged'] ?? false);
        $validated['moderation_results'] = json_encode([
            'title' => $titleModeration,
            'content' => $contentModeration,
            'checked_at' => now()->toDateTimeString()
        ]);
        
        // Create the announcement
        $announcement = Announcement::create($validated);
        
        // Redirect with appropriate message
        if ($validated['status'] === 'draft') {
            return redirect()->route('announcements.my-announcements', ['status' => 'draft'])
                ->with('success', 'Announcement saved as draft successfully.');
        } elseif ($announcementType === 'official' && isset($validated['needs_verification']) && $validated['needs_verification']) {
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
        
        // Increment view count
        $announcement->increment('view_count');
        
        // Return view with single announcement
        return view('announcements.show', compact('announcement', 'user'));
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
            'expiry_date' => 'nullable|date|after_or_equal:publish_date',
            'announcement_type' => 'required|in:official,unofficial',
            'status' => 'required|in:draft,published,pending_verification',
            'remove_image' => 'nullable|boolean',
        ];
        
        // Validate all fields at once
        $validated = $request->validate($validationRules);
        
        // ============================================
        // MODERATION CHECK - Check updated title and content
        // ============================================
        $titleModeration = $this->localMod->analyzeText($validated['title'], ['toxicity', 'pii', 'spam']);
        $contentModeration = $this->localMod->analyzeText($validated['content'], ['toxicity', 'pii', 'spam']);
        
        // If moderation fails, block the update
        if (($titleModeration['flagged'] ?? false) || ($contentModeration['flagged'] ?? false)) {
            $violations = [];
            
            if ($titleModeration['flagged'] ?? false) {
                foreach ($titleModeration['results'] ?? [] as $v) {
                    if ($v['flagged'] ?? false) {
                        $violations[] = "Title contains {$v['classifier']}";
                    }
                }
            }
            
            if ($contentModeration['flagged'] ?? false) {
                foreach ($contentModeration['results'] ?? [] as $v) {
                    if ($v['flagged'] ?? false) {
                        $violations[] = "Content contains {$v['classifier']}";
                    }
                }
            }
            
            $violationText = implode(', ', $violations);
            $errorMessage = "Your updated announcement was blocked by our content moderation system. " .
                           "Please remove inappropriate language. " .
                           "Detected: {$violationText}";
            
            return back()->withErrors(['moderation' => $errorMessage])->withInput();
        }
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
        
        // Save moderation results (optional - for audit)
        $validated['moderation_flagged'] = ($titleModeration['flagged'] ?? false) || ($contentModeration['flagged'] ?? false);
        $validated['moderation_results'] = json_encode([
            'title' => $titleModeration,
            'content' => $contentModeration,
            'checked_at' => now()->toDateTimeString()
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
        // Fetch announcements that are:
        // 1. Published/approved
        // 2. Marked as featured (you can add a 'is_featured' column to your announcements table)
        // Or you can fetch based on priority (urgent/important) to show in carousel
        
        $announcements = Announcement::with('author')
            ->where('status', 'published') // Only published announcements
            ->where(function($query) {
                // Show urgent/important announcements or those marked as featured
                $query->whereIn('priority', ['urgent', 'important'])
                      ->orWhere('is_featured', true);
            })
            ->orderByRaw("FIELD(priority, 'urgent', 'important', 'normal')")
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Add view counts and comments count (if you have these relationships)
        foreach ($announcements as $announcement) {
            $announcement->views = $announcement->views()->count() ?? 0;
            $announcement->comments_count = $announcement->comments()->count() ?? 0;
        }
        
        return response()->json([
            'success' => true,
            'announcements' => $announcements
        ]);
    } catch (\Exception $e) {
        \Log::error('Error fetching featured announcements: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to load featured announcements'
        ], 500);
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
        $draftCount = Announcement::where('author_id', $user->id)->where('status', 'draft')->count();
        $pendingCount = Announcement::where('author_id', $user->id)->where('status', 'pending_verification')->count();
        $rejectedCount = Announcement::where('author_id', $user->id)->where('status', 'rejected')->count();
        
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
            'draftCount',
            'pendingCount',
            'rejectedCount'
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
            \Log::info('Approve method called for ID: ' . $id);
            \Log::info('Request method: ' . $request->method());
            \Log::info('User: ' . auth()->user()->id . ', Role: ' . auth()->user()->role);
            
            $announcement = Announcement::findOrFail($id);
            $user = auth()->user();
            
            // Check authorization
            if (!in_array($user->role, ['admin', 'staff'])) {
                \Log::warning('Unauthorized user tried to approve: ' . $user->role);
                return response()->json(['success' => false, 'message' => 'Unauthorized. Only admin/staff can approve announcements.'], 403);
            }
            
            // Check if announcement is pending verification
            if ($announcement->status !== 'pending_verification') {
                \Log::warning('Announcement not pending: ' . $announcement->status);
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
            
            \Log::info('Announcement approved successfully: ' . $announcement->id);
            
            return response()->json([
                'success' => true, 
                'message' => 'Announcement "' . $announcement->title . '" has been approved and published.',
                'data' => $announcement
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Approval failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
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
        if ($announcement->is_official && $announcement->needs_verification) {
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
        $announcements = Announcement::where('status', 'published')
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
}