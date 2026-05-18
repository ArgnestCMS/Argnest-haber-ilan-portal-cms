<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumTopic extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'forum_category_id',
        'user_id',
        'title',
        'slug',
        'content',
        'status',
        'is_pinned',
        'is_locked',
        'is_solved',
        'replies_closed',
        'slow_mode_seconds',
        'moderator_note',
        'views',
        'last_post_at',
        'last_post_user_id',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'is_solved' => 'boolean',
        'replies_closed' => 'boolean',
        'slow_mode_seconds' => 'integer',
        'last_post_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ForumCategory::class, 'forum_category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lastPostUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_post_user_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(ForumPost::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(ForumTopicLike::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(ForumTopicBookmark::class);
    }

    public function approvedPosts(): HasMany
    {
        return $this->hasMany(ForumPost::class)
            ->with(['parent.user', 'quotedPost.user'])
            ->where('status', 'approved')
            ->oldest();
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeActiveOrder($query)
    {
        return $query
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_post_at')
            ->latest();
    }

    public function scopeTrending($query)
    {
        return $query
            ->where('created_at', '>=', now()->subDays(14))
            ->orderByDesc('is_pinned')
            ->orderByDesc('views')
            ->orderByDesc('last_post_at');
    }

    public function likedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->likes->contains('user_id', $user->id);
    }

    public function bookmarkedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->bookmarks->contains('user_id', $user->id);
    }

    public function acceptsReplies(): bool
    {
        return ! $this->is_locked && ! $this->replies_closed;
    }
}
