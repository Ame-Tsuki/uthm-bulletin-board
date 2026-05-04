<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image',
        'category',
        'priority',
        'status',
        'is_official',
        'attachments',
        'faculty',
        'author_id',
        'published_at',
        'expiry_date',
        'is_featured',
        'featured_image',
        'featured_order',
        'featured_at',
        'view_count',           // Add this for view tracking
        'needs_verification',   // Add this for approval system
        'verified_at',          // Add this for approval system
        'verified_by',          // Add this for approval system
        'rejection_reason',     // Add this for approval system
        'rejected_at',          // Add this for approval system
        'rejected_by',          // Add this for approval system
        'department',           // Add this for categorization
        'moderation_flagged',   // Add this for content moderation
        'moderation_results',   // Add this for content moderation
    ];

    protected $casts = [
        'is_official' => 'boolean',
        'published_at' => 'datetime',
        'expiry_date' => 'date',
        'attachments' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',      // Add this
        'featured_at' => 'datetime',      // Add this
        'verified_at' => 'datetime',      // Add this
        'rejected_at' => 'datetime',      // Add this
        'moderation_flagged' => 'boolean', // Add this
        'needs_verification' => 'boolean', // Add this
        'view_count' => 'integer',        // Add this
    ];

    protected $appends = ['image_url', 'featured_image_url'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the full image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        // If it's already a URL, return as-is
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        // If it's a storage path, construct the URL
        return asset('storage/' . $this->image);
    }

    /**
     * Get the featured image URL (for carousel)
     */
    public function getFeaturedImageUrlAttribute(): string
    {
        // If custom featured image is set, use it
        if ($this->featured_image) {
            return $this->featured_image;
        }
        
        // If announcement has an uploaded image, use it
        if ($this->image_url) {
            return $this->image_url;
        }
        
        // Return category-specific default images
        $defaultImages = [
            'urgent' => 'https://picsum.photos/id/0/600/400',
            'important' => 'https://picsum.photos/id/26/600/400',
            'academic' => 'https://picsum.photos/id/20/600/400',
            'events' => 'https://picsum.photos/id/29/600/400',
            'general' => 'https://picsum.photos/id/91/600/400',
        ];
        
        return $defaultImages[$this->category] ?? 'https://picsum.photos/id/20/600/400';
    }

    /**
     * Get only featured announcements
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                     ->where('status', 'published')
                     ->orderBy('featured_order', 'asc');
    }

    /**
     * Get only published announcements
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Get only pending verification announcements
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending_verification');
    }

    /**
     * Get announcements by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Get announcements by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Increment view count
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    /**
     * Check if announcement is urgent
     */
    public function isUrgent(): bool
    {
        return $this->priority === 'urgent';
    }

    /**
     * Check if announcement is important
     */
    public function isImportant(): bool
    {
        return $this->priority === 'important';
    }

    /**
     * Check if announcement is featured
     */
    public function isFeatured(): bool
    {
        return $this->is_featured && $this->status === 'published';
    }

    /**
     * Get priority badge class
     */
    public function getPriorityBadgeClass(): string
    {
        return match($this->priority) {
            'urgent' => 'bg-red-100 text-red-800',
            'important' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get category badge class
     */
    public function getCategoryBadgeClass(): string
    {
        return match($this->category) {
            'urgent' => 'bg-red-100 text-red-800',
            'important' => 'bg-yellow-100 text-yellow-800',
            'academic' => 'bg-blue-100 text-blue-800',
            'events' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}