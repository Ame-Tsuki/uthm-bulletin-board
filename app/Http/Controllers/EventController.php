<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get events for the current month by default
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));
        
        $events = Event::where('user_id', $user->id)
            ->where(function($query) use ($year, $month) {
                $query->whereYear('start_date', $year)
                    ->whereMonth('start_date', $month);
            })
            ->orWhere(function($query) use ($year, $month) {
                $query->whereYear('end_date', $year)
                    ->whereMonth('end_date', $month);
            })
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->get()
            ->map(function($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->all_day 
                        ? $event->start_date->format('Y-m-d')
                        : $event->start_date->format('Y-m-d') . 'T' . $event->start_time,
                    'end' => $event->end_date && $event->end_time
                        ? $event->end_date->format('Y-m-d') . 'T' . $event->end_time
                        : ($event->end_date 
                            ? $event->end_date->format('Y-m-d')
                            : $event->start_date->format('Y-m-d')),
                    'allDay' => $event->all_day,
                    'color' => $this->getEventColor($event->type),
                    'type' => $event->type,
                    'location' => $event->location,
                    'description' => $event->description,
                    'className' => 'cursor-pointer hover:opacity-90',
                    'extendedProps' => [
                        'type' => $event->type,
                        'location' => $event->location,
                        'description' => $event->description,
                        'user_id' => $event->user_id
                    ]
                ];
            });

        return response()->json($events);
    }

    public function store(Request $request)
{
    // Debug: Log the incoming request
    \Log::info('Event store request:', $request->all());
    
    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'start_date' => 'required|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'start_time' => 'nullable|date_format:H:i',
        'end_time' => 'nullable|date_format:H:i',
        'location' => 'nullable|string|max:255',
        'type' => 'required|in:lecture,deadline,exam,social,workshop,other',
        'all_day' => 'boolean',
        'set_reminder' => 'boolean'
    ]);

    if ($validator->fails()) {
        \Log::error('Validation failed:', $validator->errors()->toArray());
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
            'user_id' => Auth::id()
        ];

        \Log::info('Creating event with data:', $eventData);
        
        $event = Event::create($eventData);

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully!',
            'event' => $event
        ]);
    } catch (\Exception $e) {
        \Log::error('Error creating event: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error creating event: ' . $e->getMessage()
        ], 500);
    }
}

    public function update(Request $request, Event $event)
    {
        // Check if user owns the event
        if ($event->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'location' => 'nullable|string|max:255',
            'type' => 'required|in:lecture,deadline,exam,social,workshop,other'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $event->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully!',
            'event' => $event
        ]);
    }

    public function destroy(Event $event)
    {
        // Check if user owns the event
        if ($event->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully!'
        ]);
    }

    public function getUpcomingEvents()
    {
        $events = Event::where('user_id', Auth::id())
            ->where('start_date', '>=', now()->toDateString())
            ->where('start_date', '<=', now()->addDays(7)->toDateString())
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        return response()->json($events);
    }

    public function getStatistics()
    {
        $user = Auth::user();
        
        $currentMonth = date('n');
        $currentYear = date('Y');
        
        $lectures = Event::where('user_id', $user->id)
            ->where('type', 'lecture')
            ->whereMonth('start_date', $currentMonth)
            ->whereYear('start_date', $currentYear)
            ->count();

        $deadlines = Event::where('user_id', $user->id)
            ->where('type', 'deadline')
            ->where('start_date', '>=', now()->toDateString())
            ->count();

        $exams = Event::where('user_id', $user->id)
            ->where('type', 'exam')
            ->where('start_date', '>=', now()->toDateString())
            ->count();

        return response()->json([
            'lectures' => $lectures,
            'deadlines' => $deadlines,
            'exams' => $exams
        ]);
    }

    private function getEventColor($type)
    {
        $colors = [
            'lecture' => '#0056a6',
            'deadline' => '#dc3545',
            'exam' => '#6f42c1',
            'social' => '#6ea342',
            'workshop' => '#ffc107',
            'other' => '#6c757d'
        ];

        return $colors[$type] ?? '#6c757d';
    }
}