<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnouncementReport extends Model
{
    protected $fillable = [
        'announcement_id',
        'reporter_id',
        'category',
        'reason',
        'priority',
        'status',
        'resolution_note',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public static function priorityForCategory(string $category): string
    {
        return match ($category) {
            'harassment', 'inappropriate' => 'high',
            'spam', 'misinformation' => 'medium',
            default => 'low',
        };
    }

    public function toModerationArray(): array
    {
        $this->loadMissing(['announcement.author', 'reporter', 'resolver']);

        return [
            'id' => $this->id,
            'announcement_id' => $this->announcement_id,
            'announcement_title' => $this->announcement?->title ?? 'Unknown',
            'announcement_content' => $this->announcement?->content ?? '',
            'announcement_author' => $this->announcement?->author?->name ?? 'Unknown',
            'announcement_is_banned' => (bool) ($this->announcement?->isBanned() ?? false),
            'announcement_status' => $this->announcement?->status,
            'reporter_name' => $this->reporter?->name ?? 'Unknown',
            'reason' => $this->reason,
            'category' => $this->category,
            'priority' => $this->priority,
            'status' => $this->status,
            'resolution_note' => $this->resolution_note,
            'resolved_by_name' => $this->resolver?->name,
            'resolved_at' => $this->resolved_at,
            'created_at' => $this->created_at,
        ];
    }
}
