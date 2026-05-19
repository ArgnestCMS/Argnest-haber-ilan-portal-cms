<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrivateMessage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'body',
        'status',
        'ai_risk_score',
        'ai_risk_label',
        'ai_risk_reasons',
        'ai_review_required',
        'edited_at',
    ];

    protected function casts(): array
    {
        return [
            'ai_risk_score' => 'integer',
            'ai_risk_reasons' => 'array',
            'ai_review_required' => 'boolean',
            'edited_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(PrivateMessageReaction::class);
    }

    public function mediaAssets(): MorphMany
    {
        return $this->morphMany(MediaAsset::class, 'attachable')
            ->ready()
            ->oldest();
    }

    public function reactionSummary(): array
    {
        return $this->reactions
            ->groupBy('reaction')
            ->map(fn ($items) => $items->count())
            ->all();
    }

    public function canBeEditedBy(User $user): bool
    {
        return $this->sender_id === $user->id
            && ! $this->trashed()
            && $this->created_at?->gt(now()->subMinutes(10));
    }

    public function canBeDeletedBy(User $user): bool
    {
        return $this->sender_id === $user->id && ! $this->trashed();
    }
}
