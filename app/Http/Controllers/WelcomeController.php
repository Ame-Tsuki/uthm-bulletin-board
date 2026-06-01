<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\CommunityGroup;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function index(): View
    {
        $stats = [
            'active_users' => User::count(),
            'announcements_this_month' => $this->announcementsThisMonthCount(),
            'upcoming_events' => Event::where('start_date', '>=', now()->toDateString())->count(),
            'active_clubs' => CommunityGroup::count(),
        ];

        $latestAnnouncements = $this->latestAnnouncements();

        return view('welcome', compact('stats', 'latestAnnouncements'));
    }

    private function announcementsThisMonthCount(): int
    {
        $query = Announcement::query()->visibleOnBoard();

        if (Schema::hasColumn('announcements', 'is_banned')) {
            $query->notBanned();
        }

        return $query
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    private function latestAnnouncements()
    {
        $query = $this->officialAnnouncementsQuery();

        return $query
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->latest()
            ->take(3)
            ->get(['id', 'title', 'created_at', 'published_at']);
    }

    private function officialAnnouncementsQuery()
    {
        $query = Announcement::query()->visibleOnBoard();

        if (Schema::hasColumn('announcements', 'is_official')) {
            $query->where('is_official', true);
        }

        if (Schema::hasColumn('announcements', 'is_banned')) {
            $query->notBanned();
        }

        if (Schema::hasColumn('announcements', 'is_active')) {
            $query->where('is_active', 1);
        }

        return $query;
    }
}
