<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PushSubscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'endpoint',
        'endpoint_hash',
        'public_key',
        'auth_token',
        'content_encoding',
        'user_agent',
        'is_enabled',
        'preferences',
        'last_used_at',
        'failed_at',
        'failure_count',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'preferences' => 'array',
        'last_used_at' => 'datetime',
        'failed_at' => 'datetime',
        'failure_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function allows(string $notificationType): bool
    {
        $preferences = $this->preferences ?? [];

        if (($preferences['enabled'] ?? true) === false) {
            return false;
        }

        $types = $preferences['types'] ?? [];

        return array_key_exists($notificationType, $types)
            ? (bool) $types[$notificationType]
            : true;
    }

    public function markFailure(): void
    {
        $this->forceFill([
            'failed_at' => now(),
            'failure_count' => $this->failure_count + 1,
            'is_enabled' => $this->failure_count + 1 < 5,
        ])->save();
    }

    public function markSent(): void
    {
        $this->forceFill([
            'last_used_at' => now(),
            'failed_at' => null,
            'failure_count' => 0,
        ])->save();
    }

    public static function hashEndpoint(string $endpoint): string
    {
        return hash('sha256', $endpoint);
    }
}
