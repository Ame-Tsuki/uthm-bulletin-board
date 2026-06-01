<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AnnouncementCalendarService
{
    /**
     * Build calendar export data for an announcement.
     */
    public function forAnnouncement(Announcement $announcement, ?int $userId = null): array
    {
        $start = $this->resolveStart($announcement);
        $end = $this->resolveEnd($announcement, $start);
        $allDay = $this->isAllDay($announcement, $start, $end);

        $title = $announcement->title;
        $description = $this->buildDescription($announcement);
        $location = $this->buildLocation($announcement);
        $url = route('announcements.show', $announcement->id);
        $inUserCalendar = $userId ? $this->isInUserCalendar($announcement, $userId) : false;

        return [
            'title' => $title,
            'description' => $description,
            'location' => $location,
            'url' => $url,
            'start' => $start,
            'end' => $end,
            'all_day' => $allDay,
            'in_user_calendar' => $inUserCalendar,
            'calendar_url' => route('calendar'),
            'ics' => $this->buildIcs($announcement, $start, $end, $allDay, $title, $description, $location, $url),
        ];
    }

    public function isInUserCalendar(Announcement $announcement, int $userId): bool
    {
        return Event::where('user_id', $userId)
            ->where('announcement_id', $announcement->id)
            ->exists();
    }

    /**
     * Add announcement to the authenticated user's system calendar (events table).
     */
    public function addToUserCalendar(Announcement $announcement, User $user): Event
    {
        if ($this->isInUserCalendar($announcement, $user->id)) {
            return Event::where('user_id', $user->id)
                ->where('announcement_id', $announcement->id)
                ->firstOrFail();
        }

        $start = $this->resolveStart($announcement);
        $end = $this->resolveEnd($announcement, $start);
        $allDay = $this->isAllDay($announcement, $start, $end);

        return Event::create([
            'title' => $announcement->title,
            'description' => $this->buildDescription($announcement),
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'start_time' => $allDay ? null : $start->format('H:i:s'),
            'end_time' => $allDay ? null : $end->format('H:i:s'),
            'location' => $this->buildLocation($announcement),
            'type' => $this->mapEventType($announcement),
            'all_day' => $allDay,
            'set_reminder' => in_array($announcement->priority, ['urgent', 'important']),
            'visibility' => 'private',
            'user_id' => $user->id,
            'announcement_id' => $announcement->id,
        ]);
    }

    protected function mapEventType(Announcement $announcement): string
    {
        if (in_array($announcement->priority, ['urgent', 'important'])) {
            return 'important';
        }

        return match ($announcement->category) {
            'academic' => 'lecture',
            'events' => 'social',
            'urgent', 'important' => 'important',
            default => 'other',
        };
    }

    public function resolveStart(Announcement $announcement): Carbon
    {
        if ($announcement->published_at) {
            return $announcement->published_at->copy();
        }

        if ($announcement->created_at) {
            return $announcement->created_at->copy();
        }

        return now();
    }

    public function resolveEnd(Announcement $announcement, Carbon $start): Carbon
    {
        if ($announcement->expiry_date) {
            $expiryEnd = $announcement->expiry_date->copy()->endOfDay();

            if ($expiryEnd->greaterThan($start)) {
                return $expiryEnd;
            }

            return $announcement->expiry_date->copy()->endOfDay();
        }

        if ($this->isAllDay($announcement, $start, $start->copy()->endOfDay())) {
            return $start->copy()->endOfDay();
        }

        return $start->copy()->addHour();
    }

    public function isAllDay(Announcement $announcement, Carbon $start, Carbon $end): bool
    {
        if ($announcement->expiry_date) {
            return true;
        }

        return $start->format('H:i:s') === '00:00:00'
            && $end->format('H:i:s') === '23:59:59';
    }

    protected function buildDescription(Announcement $announcement): string
    {
        $parts = [
            Str::limit(strip_tags($announcement->content ?? ''), 500),
            '',
            'Author: ' . ($announcement->author->name ?? 'UTHM Bulletin Board'),
            'View announcement: ' . route('announcements.show', $announcement->id),
        ];

        if ($announcement->expiry_date) {
            $parts[] = 'Expires: ' . $announcement->expiry_date->format('F j, Y');
        }

        return implode("\n", array_filter($parts));
    }

    protected function buildLocation(Announcement $announcement): string
    {
        $parts = array_filter([
            $announcement->department ?? null,
            $announcement->faculty ?? null,
        ]);

        if (!empty($parts)) {
            return implode(', ', $parts) . ' — UTHM';
        }

        return 'UTHM Digital Bulletin Board';
    }

    protected function buildIcs(
        Announcement $announcement,
        Carbon $start,
        Carbon $end,
        bool $allDay,
        string $title,
        string $description,
        string $location,
        string $url
    ): string {
        $uid = 'announcement-' . $announcement->id . '@uthm-bulletin-board';
        $dtstamp = now()->utc()->format('Ymd\THis\Z');
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//UTHM Bulletin Board//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:' . $uid,
            'DTSTAMP:' . $dtstamp,
        ];

        if ($allDay) {
            $lines[] = 'DTSTART;VALUE=DATE:' . $start->format('Ymd');
            $lines[] = 'DTEND;VALUE=DATE:' . $end->copy()->addDay()->format('Ymd');
        } else {
            $lines[] = 'DTSTART:' . $start->utc()->format('Ymd\THis\Z');
            $lines[] = 'DTEND:' . $end->utc()->format('Ymd\THis\Z');
        }

        $lines = array_merge($lines, [
            'SUMMARY:' . $this->escapeIcs($title),
            'DESCRIPTION:' . $this->escapeIcs($description),
            'LOCATION:' . $this->escapeIcs($location),
            'URL:' . $this->escapeIcs($url),
            'STATUS:CONFIRMED',
            'SEQUENCE:0',
            'END:VEVENT',
            'END:VCALENDAR',
        ]);

        return implode("\r\n", $lines) . "\r\n";
    }

    protected function buildQuery(array $params): string
    {
        return http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    protected function escapeIcs(string $value): string
    {
        $value = str_replace(["\r\n", "\r", "\n"], '\n', $value);

        return preg_replace('/([\\\\;,])/', '\\\\$1', $value);
    }
}
