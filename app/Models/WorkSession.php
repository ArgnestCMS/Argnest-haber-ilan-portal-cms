<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkSession extends Model
{
    protected $fillable = [

        'user_id',
        'type',
        'status',
        'started_at',
        'ended_at',
        'duration_minutes',
        'ip_address',
        'device',
        'browser',
        'platform',
        'note',

    ];

    protected $casts = [

        'started_at' => 'datetime',
        'ended_at' => 'datetime',

    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}