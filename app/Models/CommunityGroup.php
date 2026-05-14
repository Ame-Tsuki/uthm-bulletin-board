<?php
// app/Models/CommunityGroup.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'category', 'cover_image', 'created_by',
        'privacy', 'max_members', 'allow_posts', 'allow_events',
        'require_approval', 'tags', 'settings', 'member_count'
    ];

    protected $casts = [
        'tags' => 'array',
        'settings' => 'array',
        'allow_posts' => 'boolean',
        'allow_events' => 'boolean',
        'require_approval' => 'boolean'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->hasMany(GroupMember::class, 'group_id'); // Add foreign key
    }

    public function posts()
    {
        return $this->hasMany(GroupPost::class, 'group_id'); // Add foreign key
    }

    public function joinRequests()
    {
        return $this->hasMany(GroupJoinRequest::class, 'group_id'); // Add foreign key
    }

    public function isFull()
    {
        return $this->max_members && $this->member_count >= $this->max_members;
    }

    public function canJoin($userId)
    {
        if ($this->isFull()) return false;
        $member = $this->members()->where('user_id', $userId)->first();
        return !$member || $member->status === 'rejected';
    }

    /**
     * Check if user is the creator of the group
     */
    public function isCreator($userId)
    {
        return $this->created_by == $userId;
    }

    /**
     * Check if user is an admin of the group
     */
    public function isAdmin($userId)
    {
        return $this->members()
            ->where('user_id', $userId)
            ->where('role', 'admin')
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Check if user is a member of the group
     */
    public function isMember($userId)
    {
        return $this->members()
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Get user's role in the group
     */
    public function getUserRole($userId)
    {
        if ($this->isCreator($userId)) {
            return 'creator';
        }
        
        $member = $this->members()
            ->where('user_id', $userId)
            ->first();
        
        return $member ? $member->role : null;
    }

    public function getPrivacyLabelAttribute()
{
    return [
        'public' => 'Public',
        'by_approval' => 'By Approval',
        'private' => 'Private',
    ][$this->privacy] ?? ucfirst($this->privacy);
}

    /**
     * Get user's membership status
     */
    public function getUserStatus($userId)
    {
        $member = $this->members()
            ->where('user_id', $userId)
            ->first();
        
        return $member ? $member->status : null;
    }
}