<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function topics(): HasMany
    {
        return $this->hasMany(ForumTopic::class);
    }

    public function publishedTopics(): HasMany
    {
        return $this->hasMany(ForumTopic::class)
            ->where('status', 'published')
            ->latest('last_post_at');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
