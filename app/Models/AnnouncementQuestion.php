<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnouncementQuestion extends Model
{
    protected $fillable = [
        'announcement_id',
        'user_id',
        'question_text',
        'answer_text',
        'answered_by',
        'answered_at',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
    ];

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    public function asker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function answerer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'answered_by');
    }

    public function isAnswered(): bool
    {
        return !is_null($this->answer_text);
    }
}
