<?php
// app/Models/GroupMember.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    protected $fillable = ['group_id', 'user_id', 'role', 'status', 'joined_at'];

    protected $casts = [
        'joined_at' => 'datetime'
    ];

    public function group()
    {
        return $this->belongsTo(CommunityGroup::class, 'group_id'); // Add foreign key
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Add foreign key
    }
}