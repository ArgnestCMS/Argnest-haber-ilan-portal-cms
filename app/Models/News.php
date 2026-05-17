<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
    'is_headline',
    'comments_enabled',
    'views',

    'daily_views',
    'weekly_views',
    'monthly_views',
    'trend_score',
    'last_viewed_at',
    'is_trending',
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