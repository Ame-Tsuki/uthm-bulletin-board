<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupPostLike extends Model
{
    use HasFactory;
    
    protected $table = 'group_post_likes';
    
    protected $fillable = [
        'post_id',
        'user_id'
    ];
    
    /**
     * Get the post that was liked
     */
    public function post()
    {
        return $this->belongsTo(GroupPost::class, 'post_id');
    }
    
    /**
     * Get the user who liked the post
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}