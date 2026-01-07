<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\View\View;
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
        
        // Check if user has permission to create announcements
        // You may want to add authorization here
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

        // Add author_id to the validated data
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
        $user = auth()->user();
        
        // Add authorization check if needed
        // $this->authorize('update', $announcement);
        
        return view('announcement.edit', compact('announcement', 'user'));
    }

    /**
     * Update the specified announcement in storage.
     */
    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        // Add authorization check if needed
        // $this->authorize('update', $announcement);

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

        // Update the announcement
        $announcement->update($validated);

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    /**
     * Remove the specified announcement from storage.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        // Add authorization check if needed
        // $this->authorize('delete', $announcement);
        
        // Delete the announcement
        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement deleted successfully.');
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
        
        return redirect()->route('announcements.index')
            ->with('success', 'Announcement archived successfully.');
    }

    /**
     * Publish the specified announcement.
     */
    public function publish(Announcement $announcement): RedirectResponse
    {
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
}