<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'type',
        'all_day',
        'is_recurring',
        'visibility',
        'recurrence_pattern',
        'color',
        'user_id',
        'google_event_id',
        'synced_with_google',
        'last_synced_at',
        'set_reminder',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'all_day' => 'boolean',
        'is_recurring' => 'boolean',
    ];

    /**
     * Relationship to get the user who owns/created this event
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for user() - for better readability
     * Shows who created the event
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the CSS class for the event based on its type
     */
    public function getEventClassAttribute()
    {
        $classes = [
            'lecture' => 'bg-blue-100 text-blue-800',
            'deadline' => 'bg-red-100 text-red-800',
            'exam' => 'bg-purple-100 text-purple-800',
            'social' => 'bg-green-100 text-green-800',
            'workshop' => 'bg-yellow-100 text-yellow-800',
            'important' => 'bg-purple-100 text-purple-800',
            'other' => 'bg-gray-100 text-gray-800'
        ];

        return $classes[$this->type] ?? $classes['other'];
    }

    /**
     * Get the dot class for the event based on its type
     */
    public function getDotClassAttribute()
    {
        $classes = [
            'lecture' => 'event-lecture',
            'deadline' => 'event-deadline',
            'exam' => 'event-exam',
            'social' => 'event-social',
            'workshop' => 'event-workshop',
            'important' => 'event-important',
            'other' => 'bg-gray-400'
        ];

        return $classes[$this->type] ?? $classes['other'];
    }

    /**
     * Check if the event was created by admin
     */
    public function isCreatedByAdmin(): bool
    {
        return $this->user && $this->user->role === 'admin';
    }

    /**
     * Check if the event is public (visible to all users)
     */
    public function isPublic(): bool
    {
        return $this->visibility === 'public';
    }

    /**
     * Get creator's name with role
     */
    public function getCreatorInfoAttribute(): string
    {
        if (!$this->user) {
            return 'Unknown User';
        }
        
        if ($this->user->role === 'admin') {
            return 'Admin: ' . $this->user->name;
        }
        
        return $this->user->name . ' (' . ucfirst($this->user->role) . ')';
    }
}