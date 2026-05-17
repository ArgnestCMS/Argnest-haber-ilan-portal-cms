<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPunishment extends Model
{
    protected $fillable = [
        'user_id',
        'moderator_id',
        'type',
        'reason',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isWarning(): bool
    {
        return $this->type === 'warning';
    }

    public function isMute(): bool
    {
        return $this->type === 'mute';
    }

    public function isTemporaryBan(): bool
    {
        return $this->type === 'temporary_ban';
    }

    public function isPermanentBan(): bool
    {
        return $this->type === 'permanent_ban';
    }

    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        return now()->greaterThan($this->expires_at);
    }
}