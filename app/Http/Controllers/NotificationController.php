<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Mark a specific notification as read and redirect to its destination.
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        
        // Updates 'read_at' timestamp column in database
        $notification->markAsRead(); 

        // Extract redirect URL from JSON payload or default to dashboard
        $destinationUrl = $notification->data['url'] ?? route('dashboard');

        return redirect($destinationUrl);
    }

    /**
     * Clear all unread notifications for the authenticated user at once.
     */
    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read successfully!');
    }
}