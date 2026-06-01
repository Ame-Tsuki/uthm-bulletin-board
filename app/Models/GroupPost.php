<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupPost extends Model
{
    use HasFactory;
    
    protected $table = 'group_posts';
    
    protected $fillable = [
        'group_id',
        'user_id',
        'content',
        'media',
        'is_pinned',
        'likes_count'
    ];
    
    protected $casts = [
        'is_pinned' => 'boolean',
        'likes_count' => 'integer',
    ];
    
    // Existing relationships...
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function group()
    {
        return $this->belongsTo(CommunityGroup::class, 'group_id');
    }
    
    // Add this relationship
    public function likes()
    {
        return $this->hasMany(GroupPostLike::class, 'post_id');
    }
    
    public function comments()
    {
        return $this->hasMany(GroupPostComment::class, 'post_id')->orderBy('created_at', 'desc');
    }
    
    /**
     * Check if a specific user has liked this post
     */
    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }
    
    /**
     * Toggle like for a user
     * Returns true if liked, false if unliked
     */
    public function toggleLike($userId)
    {
        $existingLike = $this->likes()->where('user_id', $userId)->first();
        
        if ($existingLike) {
            // Unlike
            $existingLike->delete();
            $this->decrement('likes_count');
            return false;
        } else {
            // Like
            $this->likes()->create(['user_id' => $userId]);
            $this->increment('likes_count');
            return true;
        }
    }
}