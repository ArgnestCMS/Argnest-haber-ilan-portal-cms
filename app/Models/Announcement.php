<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Announcement extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'summary',
        'content',
        'institution',
        'city',
        'category',
        'publish_date',
        'deadline',
        'source',
        'image',
        'document',
        'is_headline',
        'is_breaking',
        'comments_enabled',
        'is_active',
        'views',
    ];

    protected $casts = [
        'is_headline' => 'boolean',
        'is_breaking' => 'boolean',
        'comments_enabled' => 'boolean',
        'is_active' => 'boolean',
        'publish_date' => 'datetime',
        'deadline' => 'datetime',
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
            ->where('collection', 'announcement_attachment')
            ->ready()
            ->public()
            ->latest();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
