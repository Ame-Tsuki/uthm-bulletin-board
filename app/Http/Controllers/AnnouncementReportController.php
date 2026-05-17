<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementReport;
use App\Models\User; // <-- ADDED THIS IMPORT
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Notifications\NewReportNotification;
use Illuminate\Support\Facades\Notification;

class AnnouncementReportController extends Controller
{
    public function store(Request $request, Announcement $announcement): JsonResponse
    {
        $user = $request->user();

        if ($announcement->author_id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot report your own announcement.',
            ], 422);
        }

        if ($announcement->isBanned()) {
            return response()->json([
                'success' => false,
                'message' => 'This announcement is no longer available.',
            ], 422);
        }

        if (! in_array($announcement->status, ['published', 'pending_verification'])) {
            return response()->json([
                'success' => false,
                'message' => 'This announcement cannot be reported.',
            ], 422);
        }

        $existing = AnnouncementReport::where('announcement_id', $announcement->id)
            ->where('reporter_id', $user->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reported this announcement.',
            ], 422);
        }

        $validated = $request->validate([
            'category' => 'required|in:spam,inappropriate,harassment,misinformation,other',
            'reason' => 'required|string|min:10|max:1000',
        ]);

        $priority = AnnouncementReport::priorityForCategory($validated['category']);

        $reportCount = AnnouncementReport::where('announcement_id', $announcement->id)
            ->where('status', 'pending')
            ->count();

        if ($reportCount >= 2) {
            $priority = 'high';
        }

        // 1. Create the report
        $report = AnnouncementReport::create([
            'announcement_id' => $announcement->id,
            'reporter_id' => $user->id,
            'category' => $validated['category'],
            'reason' => $validated['reason'],
            'priority' => $priority,
            'status' => 'pending',
        ]);

        // 2. Fetch admins and send notification (MOVED UP HERE)
        // Note: Ensure 'role' and 'admin' exactly match your users table structure!
        $admins = User::where('role', 'admin')->get();
        
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewReportNotification($report));
        }

        // 3. Return the response LAST
        return response()->json([
            'success' => true,
            'message' => 'Thank you. Your report has been submitted for review.',
            'data' => $report,
        ]);
    }
}