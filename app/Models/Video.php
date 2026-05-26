<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    protected $fillable = [

        'user_id',
        'category_id',

        'title',
        'slug',
        'description',

        'thumbnail',

        'video_type',
        'youtube_url',
        'video_path',

        'views',

        'is_active',
        'is_featured',

        'published_at',

    ];
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
    protected function casts(): array
    {
        return [

            'is_active' => 'boolean',
            'is_featured' => 'boolean',

            'published_at' => 'datetime',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}