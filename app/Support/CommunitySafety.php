<?php

namespace App\Support;

use App\Models\Comment;
use App\Models\ForumPost;
use App\Models\ForumTopic;
use App\Models\LiveChatMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CommunitySafety
{
    private const BANNED_TERMS = [
        'spam',
        'dolandırıcılık',
        'dolandiricilik',
        'küfür1',
        'kufur1',
        'küfür2',
        'kufur2',
        'hakaret1',
        'hakaret2',
    ];

    private const TOXIC_HINTS = [
        'aptal',
        'salak',
        'gerizekali',
        'gerizekalı',
        'nefret',
        'tehdit',
        'öl',
        'ol',
    ];

    private const SUSPICIOUS_DOMAINS = [
        '.xyz',
        '.top',
        '.click',
        '.ru',
        'bit.ly',
        'tinyurl',
        't.me/',
        'wa.me/',
    ];

    public static function assess(string $content, ?User $user, string $surface): SafetyAssessment
    {
        $plain = Str::of(strip_tags($content))->replaceMatches('/\s+/', ' ')->trim()->toString();
        $lower = Str::lower($plain);
        $score = 0;
        $reasons = [];

        foreach (self::BANNED_TERMS as $term) {
            if (Str::contains($lower, Str::lower($term))) {
                $score += 45;
                $reasons[] = 'Yasakli ifade: ' . $term;
            }
        }

        $toxicHits = collect(self::TOXIC_HINTS)
            ->filter(fn (string $term) => Str::contains($lower, Str::lower($term)))
            ->values();

        if ($toxicHits->isNotEmpty()) {
            $score += min(30, $toxicHits->count() * 10);
            $reasons[] = 'Toxic ifade sinyali: ' . $toxicHits->implode(', ');
        }

        preg_match_all('/https?:\/\/|www\.|[a-z0-9\-]+\.(com|net|org|xyz|top|click|ru|info)/i', $plain, $linkMatches);
        $linkCount = count($linkMatches[0] ?? []);

        if ($linkCount >= 2) {
            $score += 35;
            $reasons[] = 'Coklu link tespit edildi';
        } elseif ($linkCount === 1) {
            $score += 10;
            $reasons[] = 'Link tespit edildi';
        }

        foreach (self::SUSPICIOUS_DOMAINS as $domain) {
            if (Str::contains($lower, $domain)) {
                $score += 25;
                $reasons[] = 'Supheli link/domain: ' . $domain;
            }
        }

        if (preg_match('/(.)\1{7,}/u', $plain)) {
            $score += 15;
            $reasons[] = 'Tekrarlayan karakter paterni';
        }

        if (Str::length($plain) > 20) {
            $uniqueRatio = count(array_unique(preg_split('//u', $plain, -1, PREG_SPLIT_NO_EMPTY) ?: [])) / max(1, Str::length($plain));

            if ($uniqueRatio < 0.18) {
                $score += 20;
                $reasons[] = 'Dusuk icerik cesitliligi';
            }
        }

        $recentCount = self::recentContentQuery($surface, $user?->id)
            ->where('created_at', '>=', now()->subMinute())
            ->count();

        if ($recentCount >= self::floodLimit($surface)) {
            $score += 30;
            $reasons[] = 'Cok hizli tekrar gonderim';
        }

        $normalized = self::normalize($plain);
        $recentSimilar = self::recentContentQuery($surface, $user?->id)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->latest()
            ->take(5)
            ->get()
            ->contains(function ($record) use ($normalized, $surface) {
                similar_text($normalized, self::normalize(self::contentFromRecord($record, $surface)), $percent);

                return $percent >= 88;
            });

        if ($recentSimilar) {
            $score += 25;
            $reasons[] = 'Benzer icerik tekrari';
        }

        $trustScore = self::trustScore($user);

        if ($trustScore >= 70) {
            $score -= 10;
            $reasons[] = 'Yuksek reputation guveni';
        } elseif ($trustScore <= 20) {
            $score += 10;
            $reasons[] = 'Dusuk reputation guveni';
        }

        $score = max(0, min(100, $score));

        return new SafetyAssessment(
            score: $score,
            label: match (true) {
                $score >= 90 => 'critical',
                $score >= 70 => 'high',
                $score >= 40 => 'medium',
                default => 'low',
            },
            reasons: array_values(array_unique($reasons)),
            trustScore: $trustScore,
        );
    }

    public static function trustScore(?User $user): int
    {
        if (! $user) {
            return 10;
        }

        $score = 35 + min(45, max(0, (int) $user->forum_reputation));

        if ($user->email_verified_at) {
            $score += 10;
        }

        if ($user->created_at?->lt(now()->subDays(30))) {
            $score += 10;
        }

        return min(100, $score);
    }

    private static function recentContentQuery(string $surface, ?int $userId): Builder
    {
        $query = match ($surface) {
            'forum_topic' => ForumTopic::query(),
            'forum_post' => ForumPost::query(),
            'live_chat' => LiveChatMessage::query(),
            'comment' => Comment::query(),
            default => Comment::query(),
        };

        return $query->when($userId, fn (Builder $builder) => $builder->where('user_id', $userId));
    }

    private static function contentFromRecord(object $record, string $surface): string
    {
        return match ($surface) {
            'forum_topic' => trim(($record->title ?? '') . ' ' . ($record->content ?? '')),
            'live_chat' => (string) ($record->message ?? ''),
            default => (string) ($record->content ?? ''),
        };
    }

    private static function floodLimit(string $surface): int
    {
        return match ($surface) {
            'forum_topic' => 2,
            'forum_post', 'comment' => 3,
            'live_chat' => 3,
            default => 3,
        };
    }

    private static function normalize(string $content): string
    {
        return Str::of($content)
            ->lower()
            ->replaceMatches('/[^\pL\pN]+/u', ' ')
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->toString();
    }
}
