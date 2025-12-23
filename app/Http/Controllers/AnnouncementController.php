<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\View\View;

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
     * Display a single announcement.
     */
    public function show(Announcement $announcement): View
    {
        // Get authenticated user
        $user = auth()->user();
        
        // Return view with single announcement
        return view('announcement.show', compact('announcement', 'user'));
    }
}