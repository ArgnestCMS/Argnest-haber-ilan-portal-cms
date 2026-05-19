<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMessageBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'blocker_id',
        'blocked_id',
        'muted_until',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'muted_until' => 'datetime',
        ];
    }

    public function blocker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocker_id');
    }

    public function blocked(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_id');
    }
}
