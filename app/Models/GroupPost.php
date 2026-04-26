<?php
// app/Models/GroupPost.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupPost extends Model
{
    protected $fillable = ['group_id', 'user_id', 'content', 'media', 'is_pinned'];

    protected $casts = [
        'media' => 'array',
        'is_pinned' => 'boolean'
    ];

    public function group()
    {
        return $this->belongsTo(CommunityGroup::class, 'group_id'); // Add foreign key
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Add foreign key
    }

    public function comments()
    {
        return $this->hasMany(GroupPostComment::class, 'post_id'); // Add foreign key
    }

    public function likes()
    {
        return $this->hasMany(GroupPostLike::class, 'post_id'); // Add foreign key
    }
}