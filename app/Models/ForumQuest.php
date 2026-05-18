<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ForumQuest extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'target',
        'xp_reward',
        'reputation_reward',
        'is_daily',
        'is_active',
    ];

    protected $casts = [
        'target' => 'integer',
        'xp_reward' => 'integer',
        'reputation_reward' => 'integer',
        'is_daily' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'forum_quest_user')
            ->withPivot(['tracked_on', 'progress', 'is_completed', 'completed_at'])
            ->withTimestamps();
    }
}
