<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gallery extends Model
{
    protected $fillable = [

        'user_id',
        'category_id',

        'title',
        'slug',
        'description',

        'cover_image',

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

    public function images(): HasMany
    {
        return $this->hasMany(GalleryImage::class)
            ->orderBy('sort_order');
    }
}