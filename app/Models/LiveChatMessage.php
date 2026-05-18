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
        'ai_risk_score',
        'ai_risk_label',
        'ai_risk_reasons',
        'ai_review_required',
        'moderated_by',
        'moderated_at',
        'moderation_note',
        'ip_address',
    ];

    protected $casts = [
        'moderated_at' => 'datetime',
        'ai_risk_score' => 'integer',
        'ai_risk_reasons' => 'array',
        'ai_review_required' => 'boolean',
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
