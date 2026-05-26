<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'commentable_id',
        'commentable_type',
        'content',
        'status',
        'ai_risk_score',
        'ai_risk_label',
        'ai_risk_reasons',
        'ai_review_required',
        'moderated_by',
        'moderated_at',
        'moderation_note',
        'ip_address',
        'is_edited',
    ];

    protected $casts = [
        'moderated_at' => 'datetime',
        'ai_risk_score' => 'integer',
        'ai_risk_reasons' => 'array',
        'ai_review_required' => 'boolean',
        'is_edited' => 'boolean',
    ];

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
