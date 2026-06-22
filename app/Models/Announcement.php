<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class Announcement extends Model
{
    /** Placeholder when an announcement has no cover image (featured carousel, etc.). */
    public const DEFAULT_IMAGE_URL = 'https://placehold.co/600x400/e2e8f0/64748b?text=No+Image';

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
        'featured_order',
        'featured_at',
        'view_count',           // Add this for view tracking
        'needs_verification',   // Add this for approval system
        'verified_at',          // Add this for approval system
        'verified_by',          // Add this for approval system
        'rejection_reason',     // Add this for approval system
        'approval_notes',       // Add this for approval system
        'rejected_at',          // Add this for approval system
        'rejected_by',          // Add this for approval system
        'department',           // Add this for categorization
        'moderation_flagged',   // Add this for content moderation
        'moderation_results',   // Add this for content moderation
        'is_banned',
        'banned_at',
        'banned_by',
        'ban_reason',
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
        'is_banned' => 'boolean',
        'banned_at' => 'datetime',
    ];

    protected $appends = ['image_url', 'featured_image_url'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(AnnouncementReport::class);
    }

    public function scopeNotBanned($query)
    {
        return $query->where('is_banned', false)->where('status', '!=', 'banned');
    }

    /**
     * Exclude expired announcements from public listings.
     */
    public function scopeNotExpired($query)
    {
        return $query->where('status', '!=', 'expired');
    }

    /**
     * Published announcements whose expiry calendar day has not passed.
     */
    public function scopeNotPastExpiry($query)
    {
        if (!Schema::hasColumn('announcements', 'expiry_date')) {
            return $query;
        }

        return $query->where(function ($q) {
            $q->whereNull('expiry_date')
              ->orWhereDate('expiry_date', '>=', now()->toDateString());
        });
    }

    /**
     * Visible on the main announcements board (published and not past expiry).
     */
    public function scopeVisibleOnBoard($query)
    {
        return $query->where('status', 'published')->notPastExpiry();
    }

    /**
     * Shown on the main index (active published + items awaiting moderation).
     */
    public function scopeListedOnMainBoard($query)
    {
        return $query->where(function ($q) {
            $q->visibleOnBoard()
              ->orWhereIn('status', ['pending_verification', 'rejected']);
        });
    }

    public function isExpired(): bool
    {
        if ($this->status === 'expired') {
            return true;
        }

        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->startOfDay()->lt(now()->startOfDay());
    }

    /**
     * Mark published announcements as expired when expiry_date has passed.
     */
    public static function expireDueAnnouncements(): int
    {
        if (!Schema::hasColumn('announcements', 'expiry_date')) {
            return 0;
        }

        $due = static::query()
            ->where('status', 'published')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', now()->toDateString());

        $count = (clone $due)->count();

        if ($count === 0) {
            return 0;
        }

        $updates = ['status' => 'expired'];

        if (Schema::hasColumn('announcements', 'is_featured')) {
            $updates['is_featured'] = false;
        }

        $due->update($updates);

        return $count;
    }

    public function isBanned(): bool
    {
        return $this->is_banned || $this->status === 'banned';
    }

    /**
     * Get the full image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->resolvePublicImageUrl($this->image);
    }

    /**
     * Image URL for the featured carousel — always this announcement's own cover image.
     * Featuring only sets is_featured; it does not use a separate image.
     */
    public function getFeaturedImageUrlAttribute(): string
    {
        return $this->image_url ?? self::DEFAULT_IMAGE_URL;
    }

    /**
     * Turn a stored image path or absolute URL into a public URL.
     */
    protected function resolvePublicImageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    /**
     * Get only featured announcements
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                     ->visibleOnBoard()
                     ->orderBy('featured_order', 'asc');
    }

    /**
     * Get only published announcements
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
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
            'academic' => 'bg-blue-100 text-blue-800',
            'events' => 'bg-purple-100 text-purple-800',
            'club' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}