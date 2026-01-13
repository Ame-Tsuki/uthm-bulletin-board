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
        'recurrence_pattern',
        'color',
        'user_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'all_day' => 'boolean',
        'is_recurring' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getEventClassAttribute()
    {
        $classes = [
            'lecture' => 'bg-blue-100 text-blue-800',
            'deadline' => 'bg-red-100 text-red-800',
            'exam' => 'bg-purple-100 text-purple-800',
            'social' => 'bg-green-100 text-green-800',
            'workshop' => 'bg-yellow-100 text-yellow-800',
            'other' => 'bg-gray-100 text-gray-800'
        ];

        return $classes[$this->type] ?? $classes['other'];
    }

    public function getDotClassAttribute()
    {
        $classes = [
            'lecture' => 'event-lecture',
            'deadline' => 'event-deadline',
            'exam' => 'event-exam',
            'social' => 'event-social',
            'workshop' => 'event-workshop',
            'other' => 'bg-gray-400'
        ];

        return $classes[$this->type] ?? $classes['other'];
    }
}