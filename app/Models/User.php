<?php

namespace App\Models;

use App\Notifications\VerifyEmailTurkish;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [

        'name',
        'email',
        'password',

        'role',
        'role_id',

        'status',
        'is_active',

        'email_verified_at',
        'suspended_until',
        'ban_reason',

        'avatar',
        'bio',
        'forum_reputation',
        'community_trust_score',
        'message_privacy',
        'forum_xp',
        'forum_level',
        'forum_streak_days',
        'forum_last_activity_date',

        'facebook',
        'twitter',
        'instagram',
        'youtube',

        'last_seen_at',
        'last_ip_address',
        'last_device',
        'last_browser',
        'last_platform',

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [

            'email_verified_at' => 'datetime',
            'suspended_until' => 'datetime',
            'last_seen_at' => 'datetime',

            'is_active' => 'boolean',
            'forum_reputation' => 'integer',
            'community_trust_score' => 'integer',
            'message_privacy' => 'string',
            'forum_xp' => 'integer',
            'forum_level' => 'integer',
            'forum_streak_days' => 'integer',
            'forum_last_activity_date' => 'date',

            'password' => 'hashed',

        ];
    }

    protected static function booted(): void
    {
        static::saving(function (User $user) {

            if ($user->role_id) {

                $role = Role::find($user->role_id);

                if ($role) {
                    $user->role = $role->slug;
                }

            }

        });
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailTurkish());
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE HELPERS
    |--------------------------------------------------------------------------
    */

    public function roleModel(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasPermission(string $permission): bool
    {
        if (! $this->roleModel) {
            return false;
        }

        return $this->roleModel->hasPermission($permission);
    }

    public function isAdmin(): bool
    {
        return $this->roleModel?->slug === 'admin';
    }

    public function isEditor(): bool
    {
        return $this->roleModel?->slug === 'editor';
    }

    public function isModerator(): bool
    {
        return $this->roleModel?->slug === 'moderator';
    }

    public function isUser(): bool
    {
        return $this->roleModel?->slug === 'user';
    }

    /*
    |--------------------------------------------------------------------------
    | NOTIFICATIONS
    |--------------------------------------------------------------------------
    */

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)
            ->latest();
    }

    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class)
            ->where('is_read', false)
            ->latest();
    }

    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function forumTopics(): HasMany
    {
        return $this->hasMany(ForumTopic::class);
    }

    public function forumPosts(): HasMany
    {
        return $this->hasMany(ForumPost::class);
    }

    public function forumTopicLikes(): HasMany
    {
        return $this->hasMany(ForumTopicLike::class);
    }

    public function forumTopicBookmarks(): HasMany
    {
        return $this->hasMany(ForumTopicBookmark::class);
    }

    public function forumBadges(): BelongsToMany
    {
        return $this->belongsToMany(ForumBadge::class)
            ->withTimestamps();
    }

    public function forumReputationEvents(): HasMany
    {
        return $this->hasMany(ForumReputationEvent::class)
            ->latest();
    }

    public function communityReports(): HasMany
    {
        return $this->hasMany(CommunityReport::class);
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_follows', 'follower_id', 'followed_id')
            ->withTimestamps();
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_follows', 'followed_id', 'follower_id')
            ->withTimestamps();
    }

    public function isFollowing(User $user): bool
    {
        if ($this->id === $user->id) {
            return false;
        }

        return $this->following()
            ->whereKey($user->id)
            ->exists();
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
            ->withPivot(['last_read_at', 'is_muted', 'muted_until'])
            ->withTimestamps();
    }

    public function sentPrivateMessages(): HasMany
    {
        return $this->hasMany(PrivateMessage::class, 'sender_id');
    }

    public function messageBlocks(): HasMany
    {
        return $this->hasMany(UserMessageBlock::class, 'blocker_id');
    }

    public function messageBlockedBy(): HasMany
    {
        return $this->hasMany(UserMessageBlock::class, 'blocked_id');
    }

    public function hasBlockedMessagesFrom(User $user): bool
    {
        return $this->messageBlocks()
            ->where('blocked_id', $user->id)
            ->exists();
    }

    public function canReceiveMessageRequestFrom(User $user): bool
    {
        if ($this->id === $user->id) {
            return false;
        }

        if ($this->hasBlockedMessagesFrom($user) || $user->hasBlockedMessagesFrom($this)) {
            return false;
        }

        return match ($this->message_privacy ?: 'followers') {
            'everyone' => true,
            'followers' => $user->isFollowing($this),
            default => false,
        };
    }

    public function forumQuests(): BelongsToMany
    {
        return $this->belongsToMany(ForumQuest::class, 'forum_quest_user')
            ->withPivot(['tracked_on', 'progress', 'is_completed', 'completed_at'])
            ->withTimestamps();
    }

    public function isOnline(): bool
    {
        return $this->last_seen_at?->gt(now()->subMinutes(5)) ?? false;
    }

    public function addForumReputation(int $points): void
    {
        $this->increment('forum_reputation', $points);
        $this->refresh();
        $this->syncForumBadges();
    }

    public function syncForumBadges(): void
    {
        $badges = [
            ['name' => 'Yeni Katilimci', 'slug' => 'yeni-katilimci', 'description' => 'Forumda ilk itibarini kazandi.', 'color' => 'blue', 'type' => 'reputation', 'threshold' => 10],
            ['name' => 'Aktif Uye', 'slug' => 'aktif-uye', 'description' => 'Toplulukta duzenli katki sagliyor.', 'color' => 'green', 'type' => 'reputation', 'threshold' => 50],
            ['name' => 'Guvenilir Uye', 'slug' => 'guvenilir-uye', 'description' => 'Forumda guclu bir itibar olusturdu.', 'color' => 'red', 'type' => 'reputation', 'threshold' => 100],
            ['name' => 'XP Toplayici', 'slug' => 'xp-toplayici', 'description' => '100 XP barajini asti.', 'color' => 'indigo', 'type' => 'xp', 'threshold' => 100],
            ['name' => 'Seviye Avcisi', 'slug' => 'seviye-avcisi', 'description' => 'Seviye 3 oldu.', 'color' => 'purple', 'type' => 'level', 'threshold' => 3],
            ['name' => 'Seri Katilimci', 'slug' => 'seri-katilimci', 'description' => '3 gunluk aktiflik serisi yakaladi.', 'color' => 'amber', 'type' => 'streak', 'threshold' => 3],
        ];

        foreach ($badges as $badgeData) {
            $currentValue = match ($badgeData['type']) {
                'xp' => (int) $this->forum_xp,
                'level' => (int) $this->forum_level,
                'streak' => (int) $this->forum_streak_days,
                default => (int) $this->forum_reputation,
            };

            if ($currentValue < $badgeData['threshold']) {
                continue;
            }

            $badge = ForumBadge::updateOrCreate(
                ['slug' => $badgeData['slug']],
                $badgeData
            );

            $this->forumBadges()->syncWithoutDetaching([$badge->id]);
        }
    }
}
