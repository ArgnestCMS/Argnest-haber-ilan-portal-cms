<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ForumBadge extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'type',
        'threshold',
        'xp_reward',
    ];

    protected $casts = [
        'threshold' => 'integer',
        'xp_reward' => 'integer',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }
}
