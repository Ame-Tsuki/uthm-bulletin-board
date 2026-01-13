<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class AnnouncementController extends Controller
{
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
            $query = Announcement::query();
            
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
            $announcements = Announcement::latest()->paginate(10);
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
     * Store a newly created announcement in storage.
     */
    public function store(Request $request): RedirectResponse
{
    // Check if is_official column exists
    $hasOfficialColumn = Schema::hasColumn('announcements', 'is_official');
    
    // Create validation rules - ADD 'status' here
    $validationRules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'category' => 'required|in:urgent,academic,events,general,important',
        'priority' => 'nullable|in:urgent,important,normal',
        'department' => 'nullable|string|max:100',
        'publish_date' => 'nullable|date',
        'expiry_date' => 'nullable|date|after_or_equal:publish_date',
        'announcement_type' => 'required|in:official,unofficial',
        'status' => 'required|in:draft,published', // ADD THIS LINE
    ];
    
    // Validate all fields at once
    $validated = $request->validate($validationRules);
    
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
    $status = $validated['status']; // Get the status from validated data
    
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
        // If status is 'draft', keep it as 'draft'
    }
    
    // Remove announcement_type from data as it's not a database column
    unset($validated['announcement_type']);
    
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
        $announcement = Announcement::find($id);
        
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
     * Update the specified announcement in storage.
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
            'category' => 'required|in:urgent,academic,events,general,important',
            'priority' => 'nullable|in:urgent,important,normal',
            'department' => 'nullable|string|max:100',
            'publish_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:publish_date',
            'announcement_type' => 'required|in:official,unofficial',
            'status' => 'required|in:draft,published,pending_verification',
        ];
        
        // Validate all fields at once
        $validated = $request->validate($validationRules);
        
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
        
        // Update the announcement
        $announcement->update($validated);
        
        // Redirect with appropriate message
        if ($validated['status'] === 'draft') {
            return redirect()->route('announcements.my-announcements', ['status' => 'draft'])
                ->with('success', 'Announcement updated as draft successfully.');
        } elseif ($announcementType === 'official' && $validated['needs_verification']) {
            return redirect()->route('announcements.my-announcements', ['status' => 'pending_verification'])
                ->with('success', 'Official announcement updated and submitted for verification.');
        } else {
            return redirect()->route('announcements.show', $announcement)
                ->with('success', 'Announcement updated successfully.');
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
        
        // Store type before deletion for redirect
        $wasOfficial = $announcement->is_official;
        
        // Delete the announcement
        $announcement->delete();

        // Redirect to user's announcements
        return redirect()->route('announcements.my-announcements')
            ->with('success', 'Announcement deleted successfully.');
    }

    /**
     * NEW: Display user's own announcements
     */
    public function myAnnouncements(Request $request): View
    {
        $user = auth()->user();
        $status = $request->get('status', 'all');
        
        // Start with user's own announcements
        $query = Announcement::where('author_id', $user->id);
        
        // Filter by status
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $announcements = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Calculate total views
        $totalViews = $announcements->sum('view_count');
        
        return view('announcements.my-announcements', compact('announcements', 'totalViews', 'user'));
    }

    /**
     * NEW: Verify an official announcement (admin/staff only)
     */
    public function verify(Announcement $announcement): RedirectResponse
    {
        $user = auth()->user();
        
        // Only admin/staff can verify
        if (!in_array($user->role, ['admin', 'staff'])) {
            abort(403, 'Unauthorized action.');
        }
        
        // Only official announcements need verification
        if (!$announcement->is_official) {
            return redirect()->back()
                ->with('error', 'Only official announcements can be verified.');
        }
        
        // Update announcement status
        $announcement->update([
            'status' => 'published',
            'needs_verification' => false,
            'verified_at' => now(),
            'verified_by' => $user->id,
        ]);
        
        return redirect()->route('announcements.verification-queue')
            ->with('success', 'Announcement verified and published successfully.');
    }

    /**
     * NEW: Reject an official announcement (admin/staff only)
     */
    public function reject(Announcement $announcement, Request $request): RedirectResponse
    {
        $user = auth()->user();
        
        // Only admin/staff can reject
        if (!in_array($user->role, ['admin', 'staff'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        // Update announcement status
        $announcement->update([
            'status' => 'rejected',
            'needs_verification' => false,
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now(),
            'rejected_by' => $user->id,
        ]);
        
        return redirect()->route('announcements.verification-queue')
            ->with('success', 'Announcement rejected successfully.');
    }

    /**
     * NEW: Show verification queue for admin/staff
     */
    public function verificationQueue(): View
    {
        $user = auth()->user();
        
        // Only admin/staff can see verification queue
        if (!in_array($user->role, ['admin', 'staff'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $announcements = Announcement::where('is_official', true)
            ->where('status', 'pending_verification')
            ->orderBy('created_at', 'asc')
            ->paginate(10);
        
        return view('announcements.verification-queue', compact('announcements', 'user'));
    }

    /**
     * Additional methods from original controller (preserved)
     */
    
    /**
     * Archive the specified announcement.
     */
    public function archive(Announcement $announcement): RedirectResponse
    {
        $announcement->update(['status' => 'archived']);
        
        // Redirect based on type
        if ($announcement->is_official) {
            return redirect()->route('announcements.official')
                ->with('success', 'Announcement archived successfully.');
        } else {
            return redirect()->route('announcements.unofficial')
                ->with('success', 'Announcement archived successfully.');
        }
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
            
        // Check if column exists
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
            
        // Check if column exists
        $hasOfficialColumn = Schema::hasColumn('announcements', 'is_official');
        
        return view('announcements.drafts', compact('announcements', 'user', 'hasOfficialColumn'));
    }

    /**
     * Display only official announcements.
     */
    public function official(): View
    {
        $user = auth()->user();
        
        // Check if the column exists
        $hasOfficialColumn = Schema::hasColumn('announcements', 'is_official');
        
        if ($hasOfficialColumn) {
            // Show only published official announcements for regular users
            // Admin/staff can also see pending verification
            $query = Announcement::where('is_official', true);
            
            if (in_array($user->role, ['admin', 'staff'])) {
                $query->whereIn('status', ['published', 'pending_verification']);
            } else {
                $query->where('status', 'published');
            }
            
            $announcements = $query->latest()->paginate(10);
        } else {
            // If column doesn't exist, create an empty paginator
            $announcements = new LengthAwarePaginator(
                new Collection(), // Empty collection
                0, // Total items
                10, // Items per page
                1 // Current page
            );
        }
        
        return view('announcements.official', compact('announcements', 'user', 'hasOfficialColumn'));
    }

    /**
     * Display only unofficial announcements.
     */
    public function unofficial(): View
    {
        $user = auth()->user();
        
        // Check if the column exists
        $hasOfficialColumn = Schema::hasColumn('announcements', 'is_official');
        
        if ($hasOfficialColumn) {
            $announcements = Announcement::where('is_official', false)
                ->where('status', 'published')
                ->latest()
                ->paginate(10);
        } else {
            // If column doesn't exist, create an empty paginator
            $announcements = new LengthAwarePaginator(
                new Collection(), // Empty collection
                0, // Total items
                10, // Items per page
                1 // Current page
            );
        }
        
        return view('announcements.unofficial', compact('announcements', 'user', 'hasOfficialColumn'));
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
        
        // Check if column exists
        if (!Schema::hasColumn('announcements', 'is_official')) {
            return redirect()->back()
                ->with('error', 'Database column not found. Please run migration first.');
        }
        
        try {
            $announcement->update([
                'is_official' => !$announcement->is_official,
                // Reset verification status when toggling
                'needs_verification' => false,
                'verified_at' => $announcement->is_official ? null : now(),
                'verified_by' => $announcement->is_official ? null : auth()->id(),
            ]);
            
            $action = $announcement->is_official ? 'marked as official' : 'marked as unofficial';
            
            // Redirect based on new status
            if ($announcement->is_official) {
                return redirect()->route('announcements.official')
                    ->with('success', "Announcement {$action} successfully.");
            } else {
                return redirect()->route('announcements.unofficial')
                    ->with('success', "Announcement {$action} successfully.");
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update announcement status: ' . $e->getMessage());
        }
    }
}