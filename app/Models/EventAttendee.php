<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAttendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'user_id', 'status'
    ];

    /**
     * The event this attendance belongs to
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * The user who is attending
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
