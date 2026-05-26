<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class News extends Model
{
    protected $fillable = [
    'category_id',
    'title',
    'slug',
    'summary',
    'content',
    'image',
    'document',
    'source',
    'publish_date',
    'end_date',
    'news_type',
    'share_facebook',
    'share_twitter',
    'is_headline',
    'is_breaking',
    'comments_enabled',
    'views',

    'daily_views',
    'weekly_views',
    'monthly_views',
    'trend_score',
    'last_viewed_at',
    'is_trending',
 ];

    protected $casts = [
        'is_headline' => 'boolean',
        'is_breaking' => 'boolean',
        'comments_enabled' => 'boolean',
        'share_facebook' => 'boolean',
        'share_twitter' => 'boolean',
        'is_trending' => 'boolean',
        'publish_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function approvedComments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')
            ->where('status', 'approved')
            ->latest();
    }

    public function contentAttachments(): MorphMany
    {
        return $this->morphMany(MediaAsset::class, 'attachable')
            ->where('collection', 'news_attachment')
            ->ready()
            ->public()
            ->latest();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $query) {
                $query->whereNull('publish_date')
                    ->orWhere('publish_date', '<=', now());
            })
            ->where(function (Builder $query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    public function recordView(): void
{
    $this->increment('views');
    $this->increment('daily_views');
    $this->increment('weekly_views');
    $this->increment('monthly_views');

    $this->update([
        'last_viewed_at' => now(),
        'trend_score' => ($this->daily_views * 5)
            + ($this->weekly_views * 2)
            + $this->monthly_views,
        'is_trending' => $this->daily_views >= 10,
    ]);
}
}
