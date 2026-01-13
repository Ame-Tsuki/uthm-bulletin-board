<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema; // Add this

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
            $announcements = Announcement::latest()->paginate(10);
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
    
    // Create validation rules
    $validationRules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'category' => 'required|in:urgent,academic,events,general,important',
        'priority' => 'nullable|in:urgent,important,normal',
        'department' => 'nullable|string|max:100',
        'publish_date' => 'nullable|date',
        'expiry_date' => 'nullable|date|after_or_equal:publish_date',
    ];
    
    // Add is_official validation if column exists
    if ($hasOfficialColumn) {
        $validationRules['is_official'] = 'required|in:0,1';
    }
    
    // Validate all fields at once
    $validated = $request->validate($validationRules);
    
    // Add author_id to the validated data
    $validated['author_id'] = auth()->id();
    
    // Set default priority if not provided
    if (!isset($validated['priority'])) {
        $validated['priority'] = 'normal';
    }
    
    // Add is_official if column exists and value is provided
    if ($hasOfficialColumn) {
        $validated['is_official'] = (bool) $validated['is_official'];
    } else {
        // Default to true if column doesn't exist yet
        $validated['is_official'] = true;
    }
    
    // Create the announcement
    $announcement = Announcement::create($validated);
    
    // Redirect based on announcement type
    if ($announcement->is_official) {
        return redirect()->route('announcements.official')
            ->with('success', 'Official announcement created successfully.');
    } else {
        return redirect()->route('announcements.unofficial')
            ->with('success', 'Unofficial announcement created successfully.');
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
        
        // Get authenticated user
        $user = auth()->user();
        
        // Return view with single announcement
        return view('announcements.show', compact('announcement', 'user'));
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit(Announcement $announcement): View
    {
        $user = auth()->user();
        
        // Check if column exists
        $hasOfficialColumn = Schema::hasColumn('announcements', 'is_official');
        
        return view('announcements.edit', compact('announcement', 'user', 'hasOfficialColumn'));
    }

    /**
     * Update the specified announcement in storage.
     */
   public function update(Request $request, Announcement $announcement): RedirectResponse
{
    // Check if is_official column exists
    $hasOfficialColumn = Schema::hasColumn('announcements', 'is_official');
    
    // Create validation rules
    $validationRules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'category' => 'required|in:urgent,academic,events,general,important',
        'priority' => 'nullable|in:urgent,important,normal',
        'department' => 'nullable|string|max:100',
        'publish_date' => 'nullable|date',
        'expiry_date' => 'nullable|date|after_or_equal:publish_date',
    ];
    
    // Add is_official validation if column exists
    if ($hasOfficialColumn) {
        $validationRules['is_official'] = 'required|in:0,1';
    }
    
    // Validate all fields at once
    $validated = $request->validate($validationRules);
    
    // Add is_official if column exists
    if ($hasOfficialColumn) {
        $validated['is_official'] = (bool) $validated['is_official'];
    }
    
    // Update the announcement
    $announcement->update($validated);
    
    // Redirect based on updated announcement type
    if ($announcement->is_official) {
        return redirect()->route('announcements.official')
            ->with('success', 'Announcement updated successfully.');
    } else {
        return redirect()->route('announcements.unofficial')
            ->with('success', 'Announcement updated successfully.');
    }
}
    /**
     * Remove the specified announcement from storage.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        // Store type before deletion for redirect
        $wasOfficial = $announcement->is_official;
        
        // Delete the announcement
        $announcement->delete();

        // Redirect based on type
        if ($wasOfficial) {
            return redirect()->route('announcements.official')
                ->with('success', 'Announcement deleted successfully.');
        } else {
            return redirect()->route('announcements.unofficial')
                ->with('success', 'Announcement deleted successfully.');
        }
    }

    /**
     * Additional CRUD methods (optional)
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
        $announcement->update(['status' => 'published']);
        
        // Redirect based on type
        if ($announcement->is_official) {
            return redirect()->route('announcements.official')
                ->with('success', 'Announcement published successfully.');
        } else {
            return redirect()->route('announcements.unofficial')
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
     * NEW: Display only official announcements.
     */
    public function official(): View
    {
        $user = auth()->user();
        
        // Check if the column exists
        $hasOfficialColumn = Schema::hasColumn('announcements', 'is_official');
        
        if ($hasOfficialColumn) {
            $announcements = Announcement::where('is_official', true)
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
        
        return view('announcements.official', compact('announcements', 'user', 'hasOfficialColumn'));
    }

    /**
     * NEW: Display only unofficial announcements.
     */
    public function unofficial(): View
    {
        $user = auth()->user();
        
        // Check if the column exists
        $hasOfficialColumn = Schema::hasColumn('announcements', 'is_official');
        
        if ($hasOfficialColumn) {
            $announcements = Announcement::where('is_official', false)
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
     * NEW: Toggle official status of an announcement.
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
                'is_official' => !$announcement->is_official
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