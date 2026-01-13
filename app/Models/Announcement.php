<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'category',
        'priority',
        'is_official',
        'attachments',
        'faculty',
        'status',
        'author_id',
        'published_at',
        'expiry_date',
    ];

    protected $casts = [
        'is_official' => 'boolean',
        'published_at' => 'datetime',
        'expiry_date' => 'date',
        'attachments' => 'array',
        'is_active' => 'boolean',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
