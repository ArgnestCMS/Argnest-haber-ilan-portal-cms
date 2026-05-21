<?php

namespace App\Filament\Pages;

use App\Models\CommunityReport;
use App\Models\ForumPost;
use App\Models\ForumTopic;
use App\Models\LiveChatMessage;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class AdminCommunityInsights extends Page
{
    protected string $view = 'filament.pages.admin-community-insights';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static ?string $navigationLabel = 'Topluluk İçgörüleri';

    protected static string|\UnitEnum|null $navigationGroup = 'Moderasyon & Guvenlik';

    protected static ?int $navigationSort = 0;

    protected static ?string $slug = 'community-insights';

    protected static ?string $title = 'Admin Analitikleri';

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('forum_moderasyonu');
    }

    public function getTitle(): string|Htmlable
    {
        return 'Admin Analitikleri ve Topluluk İçgörüleri';
    }

    public function rangeMetrics(): array
    {
        return [
            '24 Saat' => $this->metricsFor(now()->subDay()),
            '7 Gun' => $this->metricsFor(now()->subDays(7)),
            '30 Gun' => $this->metricsFor(now()->subDays(30)),
        ];
    }

    public function insightCards(): array
    {
        $since = now()->subDays(30);
        $reports = CommunityReport::query()->where('created_at', '>=', $since)->count();
        $resolved = CommunityReport::query()->where('status', 'resolved')->where('reviewed_at', '>=', $since)->count();
        $rejected = CommunityReport::query()->where('status', 'rejected')->where('reviewed_at', '>=', $since)->count();
        $allContent = $this->contentCountSince($since);
        $highRisk = $this->highRiskCountSince($since);
        $activeUsers = $this->activeUsers()->count();
        $avgTrust = (int) round((float) User::query()->avg('community_trust_score'));

        return [
            [
                'label' => '30 Gun Rapor Cozum Orani',
                'value' => $reports > 0 ? '%' . round(($resolved / $reports) * 100) : '%0',
                'detail' => $resolved . ' cozuldu, ' . $rejected . ' reddedildi',
                'tone' => 'success',
            ],
            [
                'label' => 'AI Risk Yogunlugu',
                'value' => $allContent > 0 ? '%' . round(($highRisk / $allContent) * 100, 1) : '%0',
                'detail' => $highRisk . ' yuksek riskli icerik / ' . $allContent . ' toplam icerik',
                'tone' => 'danger',
            ],
            [
                'label' => 'Aktif Katilimci',
                'value' => number_format($activeUsers),
                'detail' => 'Son 30 gun forum veya sohbette aktif',
                'tone' => 'info',
            ],
            [
                'label' => 'Ortalama Guven Skoru',
                'value' => number_format($avgTrust),
                'detail' => 'Tum kullanicilar uzerinden',
                'tone' => $avgTrust < 50 ? 'warning' : 'gray',
            ],
        ];
    }

    public function activeUsers(): Collection
    {
        $since = now()->subDays(30);
        $scores = collect();

        $this->addUserScores($scores, ForumTopic::query()->where('created_at', '>=', $since)->pluck('user_id'), 3);
        $this->addUserScores($scores, ForumPost::query()->where('created_at', '>=', $since)->pluck('user_id'), 2);
        $this->addUserScores($scores, LiveChatMessage::query()->where('created_at', '>=', $since)->pluck('user_id'), 1);

        $users = User::query()
            ->whereIn('id', $scores->keys())
            ->get(['id', 'name', 'email', 'forum_reputation', 'forum_xp', 'community_trust_score'])
            ->keyBy('id');

        return $scores
            ->map(fn (int $score, int $userId) => [
                'user' => $users->get($userId),
                'score' => $score,
            ])
            ->filter(fn (array $row) => filled($row['user']))
            ->sortByDesc('score')
            ->take(10)
            ->values();
    }

    public function topInteractedTopics(): Collection
    {
        return ForumTopic::query()
            ->with('user:id,name')
            ->withCount(['likes', 'posts'])
            ->orderByDesc('views')
            ->orderByDesc('likes_count')
            ->orderByDesc('posts_count')
            ->limit(10)
            ->get()
            ->map(fn (ForumTopic $topic) => [
                'id' => $topic->id,
                'title' => $topic->title,
                'user' => $topic->user?->name ?? 'Sistem',
                'views' => (int) $topic->views,
                'likes' => (int) $topic->likes_count,
                'posts' => (int) $topic->posts_count,
                'score' => (int) $topic->views + ((int) $topic->likes_count * 5) + ((int) $topic->posts_count * 3),
                'url' => url('/admin/forum-topics/' . $topic->id),
            ]);
    }

    public function topReportedContent(): Collection
    {
        $reports = CommunityReport::query()
            ->with('reportable')
            ->latest()
            ->limit(750)
            ->get();

        return $reports
            ->groupBy(fn (CommunityReport $report) => $report->reportable_type . ':' . $report->reportable_id)
            ->map(function (Collection $group) {
                /** @var CommunityReport $first */
                $first = $group->first();
                $subject = $first->reportable;

                if (! $subject instanceof Model) {
                    return null;
                }

                return [
                    'type' => $first->reportableLabel(),
                    'title' => $first->subjectTitle(),
                    'reports_count' => $group->count(),
                    'max_risk' => $group->max('subject_ai_risk_score'),
                    'latest' => $group->max('created_at'),
                    'url' => $this->adminUrlForSubject($subject),
                ];
            })
            ->filter()
            ->sortByDesc('reports_count')
            ->take(10)
            ->values();
    }

    public function reputationLeaders(): Collection
    {
        return User::query()
            ->orderByDesc('forum_reputation')
            ->orderByDesc('forum_xp')
            ->limit(10)
            ->get(['id', 'name', 'email', 'forum_reputation', 'forum_xp', 'forum_level', 'community_trust_score']);
    }

    public function xpLeaders(): Collection
    {
        return User::query()
            ->orderByDesc('forum_xp')
            ->orderByDesc('forum_reputation')
            ->limit(10)
            ->get(['id', 'name', 'email', 'forum_reputation', 'forum_xp', 'forum_level', 'community_trust_score']);
    }

    public function quickLinks(): array
    {
        return [
            ['label' => 'Forum Konulari', 'url' => url('/admin/forum-topics')],
            ['label' => 'Forum Cevaplari', 'url' => url('/admin/forum-posts')],
            ['label' => 'Canli Sohbet', 'url' => url('/admin/live-chat-messages')],
            ['label' => 'Topluluk Raporlari', 'url' => url('/admin/community-reports')],
            ['label' => 'Kullanicilar', 'url' => url('/admin/users')],
            ['label' => 'Guvenlik Ozeti', 'url' => url('/admin/moderator-safety-overview')],
        ];
    }

    private function metricsFor(\DateTimeInterface $since): array
    {
        return [
            'new_users' => User::query()->where('created_at', '>=', $since)->count(),
            'forum_topics' => ForumTopic::query()->where('created_at', '>=', $since)->count(),
            'forum_posts' => ForumPost::query()->where('created_at', '>=', $since)->count(),
            'chat_messages' => LiveChatMessage::query()->where('created_at', '>=', $since)->count(),
            'reports' => CommunityReport::query()->where('created_at', '>=', $since)->count(),
            'high_ai_risk' => $this->highRiskCountSince($since),
        ];
    }

    private function highRiskCountSince(\DateTimeInterface $since): int
    {
        return ForumTopic::query()->where('created_at', '>=', $since)->where('ai_risk_score', '>=', 70)->count()
            + ForumPost::query()->where('created_at', '>=', $since)->where('ai_risk_score', '>=', 70)->count()
            + LiveChatMessage::query()->where('created_at', '>=', $since)->where('ai_risk_score', '>=', 70)->count();
    }

    private function contentCountSince(\DateTimeInterface $since): int
    {
        return ForumTopic::query()->where('created_at', '>=', $since)->count()
            + ForumPost::query()->where('created_at', '>=', $since)->count()
            + LiveChatMessage::query()->where('created_at', '>=', $since)->count();
    }

    private function addUserScores(Collection $scores, Collection $userIds, int $weight): void
    {
        $userIds
            ->filter()
            ->each(function (int $userId) use ($scores, $weight) {
                $scores->put($userId, (int) $scores->get($userId, 0) + $weight);
            });
    }

    private function adminUrlForSubject(Model $subject): string
    {
        return match (true) {
            $subject instanceof ForumTopic => url('/admin/forum-topics/' . $subject->id),
            $subject instanceof ForumPost => url('/admin/forum-posts/' . $subject->id),
            $subject instanceof LiveChatMessage => url('/admin/live-chat-messages/' . $subject->id),
            default => url('/admin/community-reports'),
        };
    }
}
