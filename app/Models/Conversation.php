<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'status',
        'requested_by',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
        ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot(['last_read_at', 'is_muted', 'muted_until', 'is_pinned', 'pinned_at'])
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(PrivateMessage::class)->withTrashed()->oldest();
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(PrivateMessage::class)->withTrashed()->latestOfMany();
    }

    public function participantFor(User $user): ?ConversationParticipant
    {
        return $this->participants->firstWhere('user_id', $user->id)
            ?? $this->participants()->where('user_id', $user->id)->first();
    }

    public function otherParticipant(User $user): ?User
    {
        if ($this->relationLoaded('participants')) {
            return $this->participants
                ->firstWhere('user_id', '!=', $user->id)
                ?->user;
        }

        return $this->users->firstWhere('id', '!=', $user->id);
    }

    public function isParticipant(User $user): bool
    {
        return $this->participants()->where('user_id', $user->id)->exists();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function markReadFor(User $user): void
    {
        $this->participants()
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);
    }
}
