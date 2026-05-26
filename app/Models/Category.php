<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'is_active',
        'sort_order',
    ];

    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNewsType($query)
    {
        return $query->where('type', 'news');
    }

    public function scopeAnnouncementType($query)
    {
        return $query->where('type', 'announcement');
    }
}