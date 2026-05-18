<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    public const TYPE_GROUPS = [
        'forum' => [
            'forum_mention',
            'forum_post_quoted',
            'forum_topic_reply',
            'forum_topic_liked',
            'forum_topic_approved',
            'forum_topic_hidden',
            'forum_post_approved',
            'forum_post_rejected',
            'community_safety_alert',
        ],
        'community' => [
            'live_chat_mention',
            'live_activity',
        ],
        'moderation' => [
            'comment_pending',
            'comment_approved',
            'comment_rejected',
            'forum_topic_approved',
            'forum_topic_hidden',
            'forum_post_approved',
            'forum_post_rejected',
            'community_safety_alert',
        ],
    ];

    public const TYPE_LABELS = [
        'forum_mention' => 'Mention',
        'forum_post_quoted' => 'Alinti',
        'forum_topic_reply' => 'Cevap',
        'forum_topic_liked' => 'Begeni',
        'forum_topic_approved' => 'Konu onaylandi',
        'forum_topic_hidden' => 'Konu gizlendi',
        'forum_post_approved' => 'Cevap onaylandi',
        'forum_post_rejected' => 'Cevap reddedildi',
        'community_safety_alert' => 'AI risk uyarisi',
        'live_chat_mention' => 'Canli sohbet',
        'live_activity' => 'Canli aktivite',
        'comment_pending' => 'Yorum bekliyor',
        'comment_approved' => 'Yorum onaylandi',
        'comment_rejected' => 'Yorum reddedildi',
    ];

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'url',
        'is_read',
        'read_at',
        'data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'data' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function isUnread(): bool
    {
        return ! $this->is_read;
    }

    public function typeLabel(): string
    {
        return self::TYPE_LABELS[$this->type] ?? str($this->type)->replace('_', ' ')->title()->toString();
    }

    public function group(): string
    {
        foreach (self::TYPE_GROUPS as $group => $types) {
            if (in_array($this->type, $types, true)) {
                return $group;
            }
        }

        return 'system';
    }
}
