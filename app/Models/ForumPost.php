<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'forum_topic_id',
        'user_id',
        'parent_id',
        'quoted_post_id',
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

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ForumTopic::class, 'forum_topic_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ForumPost::class, 'parent_id');
    }

    public function quotedPost(): BelongsTo
    {
        return $this->belongsTo(ForumPost::class, 'quoted_post_id');
    }

    public function communityReports(): MorphMany
    {
        return $this->morphMany(CommunityReport::class, 'reportable');
    }

    public function mediaAssets(): MorphMany
    {
        return $this->morphMany(MediaAsset::class, 'attachable')
            ->ready()
            ->public()
            ->oldest();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
