<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\View\View;
use Illuminate\Auth\Access\AuthorizationException; // Import for clear authorization error
use Illuminate\Http\RedirectResponse;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements.
     */
    public function index(): View
    {
        // Get authenticated user
        $user = auth()->user();
        
        // Get announcements with pagination
        $announcements = Announcement::latest()->paginate(10);
        
        // Return view with data
        return view('announcement.announcement', compact('announcements', 'user'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create(): View
    {
        $user = auth()->user();
        
        // You may want to add authorization here, e.g., $this->authorize('create', Announcement::class);
        return view('announcement.create', compact('user'));
    }

    /**
     * Store a newly created announcement in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the request data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:urgent,academic,events,general,important',
            'priority' => 'nullable|in:urgent,important,normal',
            'department' => 'nullable|string|max:100',
            'publish_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:publish_date',
        ]);

        // Note: I'm assuming your Announcement model uses 'author_id' 
        // in the fillable array and is used as the foreign key to the User model.
        $validated['author_id'] = auth()->id();
        
        // Set default priority if not provided
        if (!isset($validated['priority'])) {
            $validated['priority'] = 'normal';
        }

        // Create the announcement
        Announcement::create($validated);

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    /**
     * Display a single announcement.
     */
    public function show(Announcement $announcement): View
    {
        // Get authenticated user
        $user = auth()->user();
        
        // Return view with single announcement
        return view('announcement.show', compact('announcement', 'user'));
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit(Announcement $announcement): View
    {
        // This line checks the Policy's 'update' method.
        $this->authorize('update', $announcement); 

        return view('announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        // Authorization check on update submission
        $this->authorize('update', $announcement); 
        
        // Validate and update
        $announcement->update($request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'publish_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:publish_date', // Adjusted validation
        ]));

        return redirect()->route('announcements.show', $announcement)
                         ->with('success', 'Announcement updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        // Authorization check on deletion
        $this->authorize('delete', $announcement); 

        $announcement->delete();

        return redirect()->route('announcements.index')
                         ->with('success', 'Announcement deleted successfully.');
    }
    
    // ----------------------------------------------------------------------
    // THE METHODS BELOW HAVE BEEN MOVED INSIDE THE CLASS BLOCK TO FIX THE PARSE ERROR
    // ----------------------------------------------------------------------

    /**
     * Archive the specified announcement.
     */
    public function archive(Announcement $announcement): RedirectResponse
    {
        // Authorization check for archiving (optional, but recommended)
        // e.g., $this->authorize('archive', $announcement); 
        
        $announcement->update(['status' => 'archived']);
        
        return redirect()->route('announcements.index')
            ->with('success', 'Announcement archived successfully.');
    }

    /**
     * Publish the specified announcement.
     */
    public function publish(Announcement $announcement): RedirectResponse
    {
        // Authorization check for publishing (optional, but recommended)
        // e.g., $this->authorize('publish', $announcement); 
        
        $announcement->update(['status' => 'published']);
        
        return redirect()->route('announcements.index')
            ->with('success', 'Announcement published successfully.');
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
            
        return view('announcement.announcement', compact('announcements', 'user'));
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
            
        return view('announcement.announcement', compact('announcements', 'user'));
    }
} // <-- This is now the final closing brace for the class