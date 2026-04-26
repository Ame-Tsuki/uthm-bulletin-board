<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
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
        // This overrides anything sent from the frontend
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
            'visibility' => $visibility,  // This will be 'public' for admin
            'user_id' => Auth::id()
        ];

        Log::info('Creating event with data:', $eventData);
        
        $event = Event::create($eventData);
        
        Log::info('Event created successfully. ID: ' . $event->id . ', Visibility: ' . $event->visibility);

        $message = ($visibility === 'public') 
            ? 'Important date posted to all users successfully!' 
            : 'Event created successfully!';

        return response()->json([
            'success' => true,
            'message' => $message,
            'event' => $event
        ]);
    } catch (\Exception $e) {
        Log::error('Error creating event: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error creating event: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Update an event (users can only update their own private events, admins can update public events)
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
            'all_day' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $event->update($request->all());

        $message = ($event->visibility === 'public') 
            ? 'Important date updated successfully!' 
            : 'Event updated successfully!';

        return response()->json([
            'success' => true,
            'message' => $message,
            'event' => $event
        ]);
    }

    /**
     * Delete an event (users delete private, admins delete public)
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

        $event->delete();

        $message = ($event->visibility === 'public') 
            ? 'Important date deleted successfully!' 
            : 'Event deleted successfully!';

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
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
    \Log::info('Upcoming events count: ' . $events->count());
    foreach ($events as $event) {
        \Log::info('Event: ' . $event->title . ' - Date: ' . $event->start_date . ' - Visibility: ' . $event->visibility);
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
        
        return response()->json([
            'total' => $total,
            'this_month' => $thisMonth,
            'upcoming' => $upcoming
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

        return response()->json([
            'lectures' => $lectures,
            'deadlines' => $deadlines,
            'exams' => $exams
        ]);
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
                'user_id' => $user->id
            ];
            
            Log::info('Creating public announcement with data:', $eventData);
            
            $event = Event::create($eventData);
            
            Log::info('Public announcement created successfully:', $event->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Important date posted to all students!',
                'event' => $event
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
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
        
        Log::info('Public events count for admin: ' . $events->count());
        
        return response()->json($events);
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