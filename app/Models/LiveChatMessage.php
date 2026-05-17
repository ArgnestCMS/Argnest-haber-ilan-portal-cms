<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LiveChatMessage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'message',
        'status',
        'moderated_by',
        'moderated_at',
        'moderation_note',
        'ip_address',
    ];

    protected $casts = [
        'moderated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
