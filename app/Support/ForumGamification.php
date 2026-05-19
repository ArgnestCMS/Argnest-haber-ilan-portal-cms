<?php

namespace App\Support;

use App\Models\ForumQuest;
use App\Models\ForumReputationEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ForumGamification
{
    public const EVENTS = [
        'topic_created' => ['points' => 2, 'xp' => 20, 'quest' => 'topic_created', 'description' => 'Konu acildi'],
        'post_created' => ['points' => 1, 'xp' => 12, 'quest' => 'post_created', 'description' => 'Cevap yazildi'],
        'topic_approved' => ['points' => 5, 'xp' => 35, 'quest' => 'clean_moderation', 'description' => 'Konu temiz onaylandi'],
        'post_approved' => ['points' => 3, 'xp' => 25, 'quest' => 'clean_moderation', 'description' => 'Cevap temiz onaylandi'],
        'topic_liked' => ['points' => 1, 'xp' => 10, 'quest' => 'received_like', 'description' => 'Konu begeni aldi'],
        'topic_like_removed' => ['points' => -1, 'xp' => -10, 'quest' => null, 'description' => 'Konu begenisi kaldirildi'],
        'topic_solved' => ['points' => 8, 'xp' => 50, 'quest' => 'topic_solved', 'description' => 'Konu cozuldu isaretlendi'],
        'content_rejected' => ['points' => -5, 'xp' => -10, 'quest' => null, 'description' => 'Icerik reddedildi'],
        'high_ai_risk' => ['points' => -3, 'xp' => -5, 'quest' => null, 'description' => 'Yuksek AI risk skoru'],
        'punishment' => ['points' => -15, 'xp' => -20, 'quest' => null, 'description' => 'Moderasyon cezasi'],
        'report_resolved' => ['points' => 2, 'xp' => 15, 'quest' => null, 'description' => 'Dogru topluluk raporu'],
        'report_rejected' => ['points' => -2, 'xp' => -5, 'quest' => null, 'description' => 'Hatali topluluk raporu'],
        'quest_completed' => ['points' => 0, 'xp' => 0, 'quest' => null, 'description' => 'Gorev tamamlandi'],
    ];

    public static function award(User $user, string $event, ?Model $subject = null, array $metadata = []): ?ForumReputationEvent
    {
        $config = self::EVENTS[$event] ?? null;

        if (! $config || self::shouldSkipDuplicate($user, $event, $subject)) {
            return null;
        }

        return DB::transaction(function () use ($user, $event, $subject, $metadata, $config) {
            self::ensureDefaultQuests();
            self::touchDailyStreak($user);

            $points = (int) ($metadata['points'] ?? $config['points']);
            $xp = (int) ($metadata['xp'] ?? $config['xp']);

            $user->forceFill([
                'forum_reputation' => max(0, (int) $user->forum_reputation + $points),
                'forum_xp' => max(0, (int) $user->forum_xp + $xp),
            ]);
            $user->forum_level = self::levelForXp((int) $user->forum_xp);
            $user->save();

            $record = ForumReputationEvent::create([
                'user_id' => $user->id,
                'type' => $event,
                'points' => $points,
                'xp' => $xp,
                'subject_type' => $subject?->getMorphClass(),
                'subject_id' => $subject?->getKey(),
                'description' => $metadata['description'] ?? $config['description'],
                'metadata' => $metadata,
            ]);

            self::advanceQuest($user, $config['quest'] ?? null);
            $user->refresh();
            $user->syncForumBadges();

            return $record;
        });
    }

    public static function levelForXp(int $xp): int
    {
        $level = 1;

        while ($xp >= self::xpForLevel($level + 1) && $level < 99) {
            $level++;
        }

        return $level;
    }

    public static function progressToNextLevel(User $user): array
    {
        $level = max(1, (int) $user->forum_level);
        $current = self::xpForLevel($level);
        $next = self::xpForLevel($level + 1);
        $xp = (int) $user->forum_xp;
        $earned = max(0, $xp - $current);
        $needed = max(1, $next - $current);

        return [
            'current' => $current,
            'next' => $next,
            'earned' => $earned,
            'needed' => $needed,
            'percent' => min(100, (int) round(($earned / $needed) * 100)),
        ];
    }

    public static function ensureDefaultQuests(): void
    {
        foreach (self::defaultQuests() as $quest) {
            ForumQuest::query()->updateOrCreate(
                ['slug' => $quest['slug']],
                $quest
            );
        }
    }

    private static function xpForLevel(int $level): int
    {
        return max(0, ($level - 1) * ($level - 1) * 120);
    }

    private static function touchDailyStreak(User $user): void
    {
        $today = now()->toDateString();
        $last = $user->forum_last_activity_date?->toDateString();

        if ($last === $today) {
            return;
        }

        $user->forum_streak_days = $last === now()->subDay()->toDateString()
            ? (int) $user->forum_streak_days + 1
            : 1;
        $user->forum_last_activity_date = $today;
    }

    private static function advanceQuest(User $user, ?string $questType): void
    {
        if (! $questType) {
            return;
        }

        $today = now()->toDateString();

        ForumQuest::query()
            ->where('type', $questType)
            ->where('is_active', true)
            ->get()
            ->each(function (ForumQuest $quest) use ($user, $today) {
                $pivot = DB::table('forum_quest_user')
                    ->where('forum_quest_id', $quest->id)
                    ->where('user_id', $user->id)
                    ->where('tracked_on', $today)
                    ->first();

                if ($pivot?->is_completed) {
                    return;
                }

                $progress = min($quest->target, (int) ($pivot->progress ?? 0) + 1);
                $isCompleted = $progress >= $quest->target;

                DB::table('forum_quest_user')->updateOrInsert(
                    [
                        'forum_quest_id' => $quest->id,
                        'user_id' => $user->id,
                        'tracked_on' => $today,
                    ],
                    [
                        'progress' => $progress,
                        'is_completed' => $isCompleted,
                        'completed_at' => $isCompleted ? now() : null,
                        'updated_at' => now(),
                        'created_at' => $pivot->created_at ?? now(),
                    ]
                );

                if ($isCompleted) {
                    self::award($user, 'quest_completed', $quest, [
                        'points' => $quest->reputation_reward,
                        'xp' => $quest->xp_reward,
                        'description' => $quest->name . ' gorevi tamamlandi',
                    ]);
                }
            });
    }

    private static function shouldSkipDuplicate(User $user, string $event, ?Model $subject): bool
    {
        if (! $subject) {
            return false;
        }

        if (in_array($event, ['topic_liked', 'topic_like_removed', 'quest_completed'], true)) {
            return false;
        }

        return ForumReputationEvent::query()
            ->where('user_id', $user->id)
            ->where('type', $event)
            ->where('subject_type', $subject->getMorphClass())
            ->where('subject_id', $subject->getKey())
            ->exists();
    }

    private static function defaultQuests(): array
    {
        return [
            ['name' => 'Gunun Ilk Konusu', 'slug' => 'daily-topic', 'type' => 'topic_created', 'target' => 1, 'xp_reward' => 30, 'reputation_reward' => 2, 'is_daily' => true, 'is_active' => true],
            ['name' => '3 Cevap Yaz', 'slug' => 'daily-replies', 'type' => 'post_created', 'target' => 3, 'xp_reward' => 45, 'reputation_reward' => 3, 'is_daily' => true, 'is_active' => true],
            ['name' => 'Temiz Moderasyon', 'slug' => 'daily-clean-moderation', 'type' => 'clean_moderation', 'target' => 2, 'xp_reward' => 40, 'reputation_reward' => 2, 'is_daily' => true, 'is_active' => true],
            ['name' => 'Begeni Topla', 'slug' => 'daily-like', 'type' => 'received_like', 'target' => 1, 'xp_reward' => 25, 'reputation_reward' => 1, 'is_daily' => true, 'is_active' => true],
        ];
    }
}
