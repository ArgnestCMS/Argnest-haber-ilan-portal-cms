<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CommunityReport extends Model
{
    public const REASONS = [
        'spam' => 'Spam',
        'insult' => 'Hakaret',
        'inappropriate' => 'Uygunsuz icerik',
        'misinformation' => 'Yanlis bilgi',
        'advertising' => 'Reklam',
        'other' => 'Diger',
    ];

    public const STATUSES = [
        'pending' => 'Bekliyor',
        'open' => 'Acik',
        'reviewed' => 'Incelendi',
        'rejected' => 'Reddedildi',
        'resolved' => 'Cozuldu',
    ];

    protected $fillable = [
        'user_id',
        'reportable_type',
        'reportable_id',
        'reason',
        'details',
        'status',
        'subject_ai_risk_score',
        'subject_ai_risk_label',
        'reviewed_by',
        'reviewed_at',
        'moderator_note',
        'resolution_action',
    ];

    protected $casts = [
        'subject_ai_risk_score' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    public function reportableLabel(): string
    {
        return match ($this->reportable_type) {
            ForumTopic::class => 'Forum konusu',
            ForumPost::class => 'Forum cevabi',
            LiveChatMessage::class => 'Canli sohbet mesaji',
            default => class_basename((string) $this->reportable_type),
        };
    }

    public function subjectTitle(): string
    {
        $subject = $this->reportable;

        return match (true) {
            $subject instanceof ForumTopic => $subject->title,
            $subject instanceof ForumPost => $subject->topic?->title ?? 'Forum cevabi',
            $subject instanceof LiveChatMessage => str($subject->message)->limit(80)->toString(),
            default => 'Silinmis icerik',
        };
    }

    public function subjectUrl(): ?string
    {
        $subject = $this->reportable;

        return match (true) {
            $subject instanceof ForumTopic && $subject->status === 'published' => route('forum.topics.show', $subject->slug),
            $subject instanceof ForumPost && $subject->topic?->status === 'published' => route('forum.topics.show', $subject->topic->slug),
            $subject instanceof LiveChatMessage => route('live-chat.index'),
            default => null,
        };
    }
}
