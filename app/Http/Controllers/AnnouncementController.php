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
    public function show($id): View
    {
        // #region agent log
        $logPath = base_path('.cursor/debug.log');
        if (!is_dir(dirname($logPath))) {
            mkdir(dirname($logPath), 0755, true);
        }
        $logData = [
            'location' => 'AnnouncementController.php:73',
            'message' => 'show method entry',
            'data' => [
                'route_param_id' => $id,
                'param_type' => gettype($id),
            ],
            'timestamp' => time() * 1000,
            'sessionId' => 'debug-session',
            'runId' => 'run1',
            'hypothesisId' => 'A'
        ];
        file_put_contents($logPath, json_encode($logData) . "\n", FILE_APPEND);
        // #endregion
        
        // #region agent log
        $logData2 = [
            'location' => 'AnnouncementController.php:88',
            'message' => 'Before announcement lookup',
            'data' => ['id' => $id],
            'timestamp' => time() * 1000,
            'sessionId' => 'debug-session',
            'runId' => 'run1',
            'hypothesisId' => 'B'
        ];
        file_put_contents($logPath, json_encode($logData2) . "\n", FILE_APPEND);
        // #endregion
        
        // Manually resolve the announcement since route uses {id} instead of {announcement}
        $announcement = Announcement::find($id);
        
        // #region agent log
        $logData3 = [
            'location' => 'AnnouncementController.php:95',
            'message' => 'After announcement lookup',
            'data' => [
                'announcement_found' => $announcement !== null,
                'announcement_id' => $announcement->id ?? null,
                'announcement_title' => $announcement->title ?? null,
            ],
            'timestamp' => time() * 1000,
            'sessionId' => 'debug-session',
            'runId' => 'run1',
            'hypothesisId' => 'C'
        ];
        file_put_contents($logPath, json_encode($logData3) . "\n", FILE_APPEND);
        // #endregion
        
        if (!$announcement) {
            // #region agent log
            $logData4 = [
                'location' => 'AnnouncementController.php:108',
                'message' => 'Announcement not found',
                'data' => ['id' => $id],
                'timestamp' => time() * 1000,
                'sessionId' => 'debug-session',
                'runId' => 'run1',
                'hypothesisId' => 'D'
            ];
            file_put_contents($logPath, json_encode($logData4) . "\n", FILE_APPEND);
            // #endregion
            
            abort(404, 'Announcement not found');
        }
        
        // Get authenticated user
        $user = auth()->user();
        
        // #region agent log
        $logData5 = [
            'location' => 'AnnouncementController.php:120',
            'message' => 'Before view return',
            'data' => [
                'user_id' => $user->id ?? null,
                'announcement_id' => $announcement->id ?? null,
                'author_relationship_loaded' => isset($announcement->author) ? true : false,
            ],
            'timestamp' => time() * 1000,
            'sessionId' => 'debug-session',
            'runId' => 'run1',
            'hypothesisId' => 'E'
        ];
        file_put_contents($logPath, json_encode($logData5) . "\n", FILE_APPEND);
        // #endregion
        
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