<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;

class NotificationController extends Controller
{
    /**
     * Mark a specific notification as read and redirect to its destination.
     * Skips unofficial announcement notifications.
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);

        // Extract redirect URL from JSON payload or default to dashboard
        $destinationUrl = $notification->data['url'] ?? route('dashboard');

        // If this is an announcement notification, delete it so it is removed permanently
        if (isset($notification->data['announcement_id'])) {
            $announcement = Announcement::find($notification->data['announcement_id']);

            // If announcement is unofficial, delete the notification and redirect to dashboard
            if ($announcement && !$announcement->is_official) {
                $notification->delete();
                return redirect()->route('dashboard')->with('info', 'Unofficial announcement notification removed.');
            }

            // For official announcement notifications, delete the notification to fully remove it
            $notification->delete();
            return redirect($destinationUrl);
        }

        // Non-announcement notifications: mark as read
        $notification->markAsRead();
        return redirect($destinationUrl);
    }

    /**
     * Clear all unread notifications for the authenticated user at once.
     * Only marks official announcement notifications as read.
     */
    public function markAllRead(Request $request)
    {
        $user = auth()->user();
        $notifications = $user->unreadNotifications;
        
        $markedCount = 0;
        $deletedCount = 0;
        
        foreach ($notifications as $notification) {
            // If this is an announcement notification, delete it so it's removed permanently
            if (isset($notification->data['announcement_id'])) {
                $announcement = Announcement::find($notification->data['announcement_id']);

                // If announcement is unofficial, delete the notification
                if ($announcement && !$announcement->is_official) {
                    $notification->delete();
                    $deletedCount++;
                    continue;
                }

                // Official announcement notification: delete (fully remove)
                $notification->delete();
                $deletedCount++;
                continue;
            }

            // Non-announcement notifications: mark as read
            $notification->markAsRead();
            $markedCount++;
        }
        
        $message = "All notifications processed.";
        if ($markedCount > 0 && $deletedCount > 0) {
            $message = "Marked {$markedCount} official notifications as read and removed {$deletedCount} unofficial notifications.";
        } elseif ($markedCount > 0) {
            $message = "Marked {$markedCount} notifications as read successfully!";
        } elseif ($deletedCount > 0) {
            $message = "Removed {$deletedCount} unofficial announcement notifications.";
        } else {
            $message = "All notifications marked as read successfully!";
        }
        
        // Handle different request types
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'marked_count' => $markedCount,
                'deleted_count' => $deletedCount
            ]);
        }
        
        // For web requests, check if we should redirect back or to a specific page
        if ($request->has('redirect_to')) {
            return redirect($request->redirect_to)->with('success', $message);
        }
        
        return redirect()->back()->with('success', $message);
    }
    
    /**
     * Get unread notifications count (only official announcements)
     */
    public function unreadCount()
    {
        $user = auth()->user();
        $unreadNotifications = $user->unreadNotifications;
        
        // Filter out unofficial announcement notifications from count
        $filteredCount = 0;
        foreach ($unreadNotifications as $notification) {
            if (isset($notification->data['announcement_id'])) {
                $announcement = Announcement::find($notification->data['announcement_id']);
                if ($announcement && !$announcement->is_official) {
                    continue; // Skip unofficial announcements in count
                }
            }
            $filteredCount++;
        }
        
        return response()->json([
            'count' => $filteredCount,
            'total_unread' => $unreadNotifications->count(),
            'filtered_out' => $unreadNotifications->count() - $filteredCount
        ]);
    }
    
    /**
     * Get all notifications (filtered for admin)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $notifications = $user->notifications()->paginate(20);
        
        // Filter the collection to remove unofficial announcement notifications
        $filteredNotifications = $notifications->getCollection()->filter(function ($notification) {
            if (isset($notification->data['announcement_id'])) {
                $announcement = Announcement::find($notification->data['announcement_id']);
                // Only keep if announcement is official or doesn't exist (other notification types)
                if ($announcement && !$announcement->is_official) {
                    return false; // Remove unofficial announcement notifications
                }
            }
            return true; // Keep other notifications
        });
        
        // Update the collection
        $notifications->setCollection($filteredNotifications);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'notifications' => $filteredNotifications->values(),
                'total' => $filteredNotifications->count()
            ]);
        }
        
        return view('notifications.index', compact('notifications'));
    }
    
    /**
     * Delete a specific notification
     */
    public function destroy($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully'
            ]);
        }
        
        return back()->with('success', 'Notification deleted');
    }
}