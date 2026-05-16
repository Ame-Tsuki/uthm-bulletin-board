<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Services\GoogleCalendarService;

class EventController extends Controller
{
    protected $googleCalendarService;

    /**
     * Constructor with Google Calendar Service injection
     */
    public function __construct(GoogleCalendarService $googleCalendarService)
    {
        $this->googleCalendarService = $googleCalendarService;
    }

    /**
     * Get events for the authenticated user (personal + public events)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));
        
        if ($user->role === 'admin') {
            // Admin sees all public events
            $events = Event::with('creator')
                ->where('visibility', 'public')
                ->where(function($q) use ($year, $month) {
                    $q->whereYear('start_date', $year)->whereMonth('start_date', $month)
                      ->orWhereYear('end_date', $year)->whereMonth('end_date', $month);
                })
                ->orderBy('start_date', 'asc')
                ->orderBy('start_time', 'asc')
                ->get();
        } else {
            // Regular users: their private events + public events
            $events = Event::with('creator')
                ->where(function($query) use ($user, $year, $month) {
                    $query->where('user_id', $user->id)
                        ->where('visibility', 'private')
                        ->where(function($q) use ($year, $month) {
                            $q->whereYear('start_date', $year)->whereMonth('start_date', $month)
                              ->orWhereYear('end_date', $year)->whereMonth('end_date', $month);
                        });
                })
                ->orWhere(function($query) use ($year, $month) {
                    $query->where('visibility', 'public')
                        ->where(function($q) use ($year, $month) {
                            $q->whereYear('start_date', $year)->whereMonth('start_date', $month)
                              ->orWhereYear('end_date', $year)->whereMonth('end_date', $month);
                        });
                })
                ->orderBy('start_date', 'asc')
                ->orderBy('start_time', 'asc')
                ->get();
        }
        
        return response()->json($events);
    }

    /**
     * Store a new event (personal for users, public for admin)
     * Now with Google Calendar sync
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Debug: Log the incoming request
        Log::info('Event store request:', $request->all());
        Log::info('User role: ' . $user->role);
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'type' => 'required|in:lecture,deadline,exam,social,workshop,other,important',
            'all_day' => 'boolean',
            'set_reminder' => 'boolean',
            'sync_to_google' => 'boolean', // New field to control Google sync
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // FORCE visibility: If user is admin, ALWAYS set to 'public'
            $visibility = 'private';
            
            if ($user->role === 'admin') {
                $visibility = 'public';
                Log::info('Admin user detected - FORCING visibility to PUBLIC');
            } else {
                // Non-admin users always get private
                $visibility = 'private';
            }
            
            $eventData = [
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date ?? $request->start_date,
                'start_time' => $request->boolean('all_day') ? null : ($request->start_time ?? null),
                'end_time' => $request->boolean('all_day') ? null : ($request->end_time ?? null),
                'location' => $request->location,
                'type' => $request->type,
                'all_day' => $request->boolean('all_day') ?? false,
                'set_reminder' => $request->boolean('set_reminder') ?? false,
                'visibility' => $visibility,
                'user_id' => Auth::id(),
                'synced_with_google' => false, // Will be updated if sync is successful
            ];

            Log::info('Creating event with data:', $eventData);
            
            $event = Event::create($eventData);
            
            Log::info('Event created successfully. ID: ' . $event->id . ', Visibility: ' . $event->visibility);

            // Sync to Google Calendar if user has connected their account
            if ($user->google_token && $request->boolean('sync_to_google', true)) {
                try {
                    $googleEvent = $this->googleCalendarService->syncEvent($event);
                    
                    if ($googleEvent) {
                        $event->update([
                            'google_event_id' => $googleEvent->getId(),
                            'synced_with_google' => true,
                            'last_synced_at' => now(),
                        ]);
                        
                        Log::info('Event synced to Google Calendar: ' . $event->google_event_id);
                    } else {
                        Log::warning('Failed to sync event to Google Calendar');
                    }
                } catch (\Exception $e) {
                    Log::error('Google Calendar sync error: ' . $e->getMessage());
                    // Don't fail the whole operation if sync fails
                }
            }

            $message = ($visibility === 'public') 
                ? 'Important date posted to all users successfully!' 
                : 'Event created successfully!';
            
            if ($event->synced_with_google) {
                $message .= ' (Synced to Google Calendar)';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'event' => $event->load('creator')
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Error creating event: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error creating event: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an event (users can only update their own private events, admins can update public events)
     * Now with Google Calendar sync
     */
    public function update(Request $request, Event $event)
    {
        $user = Auth::user();
        
        // Check permissions based on visibility
        if ($event->visibility === 'public') {
            // Only admin can update public events
            if ($user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized - Only administrators can modify important dates'
                ], 403);
            }
        } else {
            // Private events can only be updated by the owner
            if ($event->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized - This is not your event'
                ], 403);
            }
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'type' => 'required|in:lecture,deadline,exam,social,workshop,other,important',
            'all_day' => 'boolean',
            'set_reminder' => 'boolean',
            'sync_to_google' => 'boolean', // New field to control Google sync
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Update the event
            $event->update([
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date ?? $request->start_date,
                'start_time' => $request->boolean('all_day') ? null : ($request->start_time ?? null),
                'end_time' => $request->boolean('all_day') ? null : ($request->end_time ?? null),
                'location' => $request->location,
                'type' => $request->type,
                'all_day' => $request->boolean('all_day') ?? false,
                'set_reminder' => $request->boolean('set_reminder') ?? false,
            ]);

            Log::info('Event updated successfully. ID: ' . $event->id);

            // Sync to Google Calendar if applicable
            if ($user->google_token) {
                if ($request->boolean('sync_to_google', true)) {
                    try {
                        $googleEvent = $this->googleCalendarService->syncEvent($event);

                        if ($googleEvent) {
                            $event->update([
                                'google_event_id' => $googleEvent->getId(),
                                'synced_with_google' => true,
                                'last_synced_at' => now(),
                            ]);

                            Log::info('Updated event synced to Google Calendar: ' . $event->google_event_id);
                        } else {
                            Log::warning('Failed to sync updated event to Google Calendar');
                        }
                    } catch (\Exception $e) {
                        Log::error('Google Calendar sync error during update: ' . $e->getMessage());
                    }
                } elseif ($event->google_event_id) {
                    try {
                        $this->googleCalendarService->deleteEvent($event);
                        $event->update([
                            'google_event_id' => null,
                            'synced_with_google' => false,
                            'last_synced_at' => null,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Google Calendar unsync error during update: ' . $e->getMessage());
                    }
                }
            }

            $message = ($event->visibility === 'public') 
                ? 'Important date updated successfully!' 
                : 'Event updated successfully!';
            
            if ($event->synced_with_google) {
                $message .= ' (Synced to Google Calendar)';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'event' => $event->fresh()->load('creator')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating event: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error updating event: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an event (users delete private, admins delete public)
     * Now with Google Calendar sync
     */
    public function destroy(Event $event)
    {
        $user = Auth::user();
        
        // Check permissions based on visibility
        if ($event->visibility === 'public') {
            // Only admin can delete public events
            if ($user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized - Only administrators can delete important dates'
                ], 403);
            }
        } else {
            // Private events can only be deleted by the owner
            if ($event->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized - This is not your event'
                ], 403);
            }
        }

        try {
            // FIX: Rely entirely on the presence of the ID string to avoid tracking bugs
            if ($event->google_event_id) {
                try {
                    $this->googleCalendarService->deleteEvent($event);
                    Log::info('Event deleted from Google Calendar: ' . $event->google_event_id);
                } catch (\Exception $e) {
                    Log::error('Failed to delete event from Google Calendar: ' . $e->getMessage());
                    // Continue with local deletion even if Google deletion fails
                }
            }

            $eventTitle = $event->title;
            $wasPublic = $event->visibility === 'public';
            
            $event->delete();

            Log::info('Event deleted successfully from local database. ID: ' . $event->id);

            $message = $wasPublic 
                ? 'Important date deleted successfully!' 
                : 'Event deleted successfully!';

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting event: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting event: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get upcoming events (personal + public)
     */
    public function getUpcomingEvents(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 10);
        
        if ($user->role === 'admin') {
            // Admin sees all public events (including today and future)
            $events = Event::with('creator')
                ->where('visibility', 'public')
                ->where('start_date', '>=', now()->startOfDay()->toDateString())
                ->orderBy('start_date', 'asc')
                ->orderBy('start_time', 'asc')
                ->limit($limit)
                ->get();
        } else {
            // Regular users see their private events + public events (today and future)
            $events = Event::with('creator')
                ->where(function($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->where('visibility', 'private');
                })
                ->orWhere(function($query) {
                    $query->where('visibility', 'public');
                })
                ->where('start_date', '>=', now()->startOfDay()->toDateString())
                ->orderBy('start_date', 'asc')
                ->orderBy('start_time', 'asc')
                ->limit($limit)
                ->get();
        }
        
        // Log for debugging
        Log::info('Upcoming events count: ' . $events->count());
        foreach ($events as $event) {
            Log::info('Event: ' . $event->title . ' - Date: ' . $event->start_date . ' - Visibility: ' . $event->visibility . ' - Synced: ' . ($event->synced_with_google ? 'Yes' : 'No'));
        }
        
        return response()->json($events);
    }

    /**
     * Get event statistics
     */
    public function getStatistics(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            // Admin statistics for public events
            $total = Event::where('visibility', 'public')->count();
            
            $currentMonth = date('n');
            $currentYear = date('Y');
            $thisMonth = Event::where('visibility', 'public')
                ->whereMonth('start_date', $currentMonth)
                ->whereYear('start_date', $currentYear)
                ->count();
            
            $upcoming = Event::where('visibility', 'public')
                ->where('start_date', '>=', now()->toDateString())
                ->count();
            
            $syncedCount = Event::where('visibility', 'public')
                ->where('synced_with_google', true)
                ->count();
            
            return response()->json([
                'total' => $total,
                'this_month' => $thisMonth,
                'upcoming' => $upcoming,
                'synced_with_google' => $syncedCount
            ]);
        } else {
            // Regular users: Count BOTH their private events AND public events from admin
            $currentMonth = date('n');
            $currentYear = date('Y');
            
            // Count lectures (user's private + public events)
            $lectures = Event::where(function($query) use ($user, $currentMonth, $currentYear) {
                    // User's own private lectures
                    $query->where('user_id', $user->id)
                        ->where('visibility', 'private')
                        ->where('type', 'lecture')
                        ->whereMonth('start_date', $currentMonth)
                        ->whereYear('start_date', $currentYear);
                })
                ->orWhere(function($query) use ($currentMonth, $currentYear) {
                    // Public lectures from admin
                    $query->where('visibility', 'public')
                        ->where('type', 'lecture')
                        ->whereMonth('start_date', $currentMonth)
                        ->whereYear('start_date', $currentYear);
                })
                ->count();

            // Count deadlines (user's private + public events)
            $deadlines = Event::where(function($query) use ($user) {
                    // User's own private deadlines
                    $query->where('user_id', $user->id)
                        ->where('visibility', 'private')
                        ->where('type', 'deadline')
                        ->where('start_date', '>=', now()->toDateString());
                })
                ->orWhere(function($query) {
                    // Public deadlines from admin
                    $query->where('visibility', 'public')
                        ->where('type', 'deadline')
                        ->where('start_date', '>=', now()->toDateString());
                })
                ->count();

            // Count exams (user's private + public events)
            $exams = Event::where(function($query) use ($user) {
                    // User's own private exams
                    $query->where('user_id', $user->id)
                        ->where('visibility', 'private')
                        ->where('type', 'exam')
                        ->where('start_date', '>=', now()->toDateString());
                })
                ->orWhere(function($query) {
                    // Public exams from admin
                    $query->where('visibility', 'public')
                        ->where('type', 'exam')
                        ->where('start_date', '>=', now()->toDateString());
                })
                ->count();
            
            // Count synced events
            $syncedCount = Event::where('user_id', $user->id)
                ->where('synced_with_google', true)
                ->count();

            return response()->json([
                'lectures' => $lectures,
                'deadlines' => $deadlines,
                'exams' => $exams,
                'synced_with_google' => $syncedCount
            ]);
        }
    }

    /**
     * Sync all user's events to Google Calendar
     */
    public function syncAllToGoogle()
    {
        $user = Auth::user();
        
        if (!$user->google_token) {
            return response()->json([
                'success' => false,
                'message' => 'Google Calendar is not connected. Please connect your Google Calendar first.'
            ], 400);
        }
        
        try {
            $events = Event::where('user_id', $user->id)
                ->when($user->role !== 'admin', function ($query) {
                    $query->where('visibility', 'private');
                })
                ->get();
            
            $syncedCount = 0;
            $failedCount = 0;
            
            foreach ($events as $event) {
                try {
                    $googleEvent = $this->googleCalendarService->syncEvent($event);
                    
                    if ($googleEvent) {
                        $event->update([
                            'google_event_id' => $googleEvent->getId(),
                            'synced_with_google' => true,
                            'last_synced_at' => now(),
                        ]);
                        $syncedCount++;
                    } else {
                        $failedCount++;
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to sync event ID ' . $event->id . ': ' . $e->getMessage());
                    $failedCount++;
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => "Synced {$syncedCount} events to Google Calendar. {$failedCount} failed.",
                'synced_count' => $syncedCount,
                'failed_count' => $failedCount
            ]);
            
        } catch (\Exception $e) {
            Log::error('Bulk sync error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync events: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Create public announcement for all users
     */
    public function createPublicAnnouncement(Request $request)
    {
        $user = Auth::user();
        
        // Only admin can create public announcements
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Only administrators can create public announcements'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'type' => 'required|in:lecture,deadline,exam,social,workshop,other,important',
            'all_day' => 'boolean',
            'set_reminder' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $eventData = [
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date ?? $request->start_date,
                'start_time' => $request->boolean('all_day') ? null : ($request->start_time ?? null),
                'end_time' => $request->boolean('all_day') ? null : ($request->end_time ?? null),
                'location' => $request->location,
                'type' => $request->type,
                'all_day' => $request->boolean('all_day') ?? false,
                'set_reminder' => $request->boolean('set_reminder') ?? false,
                'visibility' => 'public', // Force public visibility
                'user_id' => $user->id,
                'synced_with_google' => false,
            ];
            
            Log::info('Creating public announcement with data:', $eventData);
            
            $event = Event::create($eventData);
            
            Log::info('Public announcement created successfully:', $event->toArray());

            // Optional: Sync admin's public announcements to their own Google Calendar
            if ($user->google_token) {
                try {
                    $googleEvent = $this->googleCalendarService->syncEvent($event);
                    
                    if ($googleEvent) {
                        $event->update([
                            'google_event_id' => $googleEvent->getId(),
                            'synced_with_google' => true,
                            'last_synced_at' => now(),
                        ]);
                        
                        Log::info('Public announcement synced to admin Google Calendar: ' . $event->google_event_id);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to sync public announcement to Google Calendar: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Important date posted to all students!',
                'event' => $event->load('creator')
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Error creating public announcement: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error creating announcement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all public events (for admin management)
     */
    public function getPublicEvents(Request $request)
    {
        $user = Auth::user();
        
        // Only admin can see all public events for management
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));
        
        // Get all public events regardless of date
        $events = Event::where('visibility', 'public')
            ->with('creator')
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
        
        Log::info('Public events count for admin: ' . $events->count());
        
        return response()->json($events);
    }

    /**
     * Toggle Google Calendar sync for a specific event
     */
    public function toggleGoogleSync(Event $event)
    {
        $user = Auth::user();
        
        // Check if user owns the event
        if ($event->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        if (!$user->google_token) {
            return response()->json([
                'success' => false,
                'message' => 'Please connect your Google Calendar first'
            ], 400);
        }
        
        try {
            if ($event->synced_with_google) {
                // Remove from Google Calendar
                $this->googleCalendarService->deleteEvent($event);
                
                $event->update([
                    'google_event_id' => null,
                    'synced_with_google' => false,
                    'last_synced_at' => null,
                ]);
                
                $message = 'Event removed from Google Calendar';
            } else {
                // Add to Google Calendar
                $googleEvent = $this->googleCalendarService->syncEvent($event);
                
                if ($googleEvent) {
                    $event->update([
                        'google_event_id' => $googleEvent->getId(),
                        'synced_with_google' => true,
                        'last_synced_at' => now(),
                    ]);
                    
                    $message = 'Event synced to Google Calendar';
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to sync event to Google Calendar'
                    ], 500);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'event' => $event->fresh()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Toggle sync error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error toggling Google Calendar sync: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get event color based on type
     */
    private function getEventColor($type)
    {
        $colors = [
            'lecture' => '#0056a6',
            'deadline' => '#dc3545',
            'exam' => '#6f42c1',
            'social' => '#6ea342',
            'workshop' => '#ffc107',
            'important' => '#8b5cf6',
            'other' => '#6c757d'
        ];

        return $colors[$type] ?? '#6c757d';
    }
}