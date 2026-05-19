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

class ModeratorSafetyOverview extends Page
{
    protected string $view = 'filament.pages.moderator-safety-overview';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $navigationLabel = 'Guvenlik Ozeti';

    protected static string|\UnitEnum|null $navigationGroup = 'Moderasyon & Guvenlik';

    protected static ?int $navigationSort = -1;

    protected static ?string $slug = 'moderator-safety-overview';

    protected static ?string $title = 'Moderator Dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('forum_moderasyonu');
    }

    public static function getNavigationBadge(): ?string
    {
        $openItems = ForumTopic::query()->where('status', 'pending')->count()
            + ForumPost::query()->where('status', 'pending')->count()
            + LiveChatMessage::query()->where('status', 'pending')->count()
            + CommunityReport::query()->whereIn('status', ['pending', 'open'])->count();

        return $openItems > 0 ? (string) $openItems : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Moderator Dashboard';
    }

    public function stats(): array
    {
        $since = now()->subDay();

        return [
            [
                'label' => 'Pending Forum Konulari',
                'value' => ForumTopic::query()->where('status', 'pending')->count(),
                'url' => url('/admin/forum-topics'),
                'tone' => 'warning',
            ],
            [
                'label' => 'Pending Forum Cevaplari',
                'value' => ForumPost::query()->where('status', 'pending')->count(),
                'url' => url('/admin/forum-posts'),
                'tone' => 'warning',
            ],
            [
                'label' => 'Pending Canli Sohbet',
                'value' => LiveChatMessage::query()->where('status', 'pending')->count(),
                'url' => url('/admin/live-chat-messages'),
                'tone' => 'info',
            ],
            [
                'label' => 'Acik Topluluk Raporlari',
                'value' => CommunityReport::query()->whereIn('status', ['pending', 'open'])->count(),
                'url' => url('/admin/community-reports'),
                'tone' => 'danger',
            ],
            [
                'label' => 'Yuksek AI Risk',
                'value' => $this->highRiskCount(),
                'url' => '#high-risk-content',
                'tone' => 'danger',
            ],
            [
                'label' => 'Son 24 Saat Rapor',
                'value' => CommunityReport::query()->where('created_at', '>=', $since)->count(),
                'url' => url('/admin/community-reports'),
                'tone' => 'gray',
            ],
        ];
    }

    public function last24HoursStats(): array
    {
        $since = now()->subDay();

        return [
            'forum_topics' => ForumTopic::query()->where('created_at', '>=', $since)->count(),
            'forum_posts' => ForumPost::query()->where('created_at', '>=', $since)->count(),
            'chat_messages' => LiveChatMessage::query()->where('created_at', '>=', $since)->count(),
            'reports' => CommunityReport::query()->where('created_at', '>=', $since)->count(),
            'resolved_reports' => CommunityReport::query()->where('status', 'resolved')->where('reviewed_at', '>=', $since)->count(),
            'rejected_reports' => CommunityReport::query()->where('status', 'rejected')->where('reviewed_at', '>=', $since)->count(),
        ];
    }

    public function highRiskContent(): Collection
    {
        return collect()
            ->merge(ForumTopic::query()
                ->with('user:id,name')
                ->where('ai_risk_score', '>=', 70)
                ->latest()
                ->limit(8)
                ->get()
                ->map(fn (ForumTopic $topic) => $this->contentRow('Forum konusu', $topic, $topic->title, url('/admin/forum-topics/' . $topic->id), $topic->user?->name)))
            ->merge(ForumPost::query()
                ->with(['user:id,name', 'topic:id,title'])
                ->where('ai_risk_score', '>=', 70)
                ->latest()
                ->limit(8)
                ->get()
                ->map(fn (ForumPost $post) => $this->contentRow('Forum cevabi', $post, $post->topic?->title ?? 'Forum cevabi', url('/admin/forum-posts/' . $post->id), $post->user?->name)))
            ->merge(LiveChatMessage::query()
                ->with('user:id,name')
                ->where('ai_risk_score', '>=', 70)
                ->latest()
                ->limit(8)
                ->get()
                ->map(fn (LiveChatMessage $message) => $this->contentRow('Canli sohbet', $message, str($message->message)->limit(80)->toString(), url('/admin/live-chat-messages/' . $message->id), $message->user?->name)))
            ->sortByDesc('risk_score')
            ->take(10)
            ->values();
    }

    public function recentModerationActions(): Collection
    {
        return collect()
            ->merge(ForumPost::query()
                ->with(['user:id,name', 'moderator:id,name', 'topic:id,title'])
                ->whereNotNull('moderated_at')
                ->latest('moderated_at')
                ->limit(8)
                ->get()
                ->map(fn (ForumPost $post) => [
                    'type' => 'Forum cevabi',
                    'action' => $post->status,
                    'title' => $post->topic?->title ?? 'Forum cevabi',
                    'moderator' => $post->moderator?->name ?? '-',
                    'user' => $post->user?->name ?? 'Sistem',
                    'time' => $post->moderated_at,
                    'url' => url('/admin/forum-posts/' . $post->id),
                ]))
            ->merge(LiveChatMessage::query()
                ->with(['user:id,name', 'moderator:id,name'])
                ->whereNotNull('moderated_at')
                ->latest('moderated_at')
                ->limit(8)
                ->get()
                ->map(fn (LiveChatMessage $message) => [
                    'type' => 'Canli sohbet',
                    'action' => $message->status,
                    'title' => str($message->message)->limit(70)->toString(),
                    'moderator' => $message->moderator?->name ?? '-',
                    'user' => $message->user?->name ?? 'Sistem',
                    'time' => $message->moderated_at,
                    'url' => url('/admin/live-chat-messages/' . $message->id),
                ]))
            ->merge(CommunityReport::query()
                ->with(['reporter:id,name', 'reviewer:id,name', 'reportable'])
                ->whereNotNull('reviewed_at')
                ->latest('reviewed_at')
                ->limit(8)
                ->get()
                ->map(fn (CommunityReport $report) => [
                    'type' => 'Topluluk raporu',
                    'action' => $report->status,
                    'title' => $report->subjectTitle(),
                    'moderator' => $report->reviewer?->name ?? '-',
                    'user' => $report->reporter?->name ?? 'Sistem',
                    'time' => $report->reviewed_at,
                    'url' => url('/admin/community-reports/' . $report->id),
                ]))
            ->sortByDesc('time')
            ->take(10)
            ->values();
    }

    public function lowTrustUsers(): Collection
    {
        return User::query()
            ->where('community_trust_score', '<', 50)
            ->orderBy('community_trust_score')
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get(['id', 'name', 'email', 'community_trust_score', 'forum_reputation']);
    }

    public function topReportedUsers(): Collection
    {
        $users = collect();

        CommunityReport::query()
            ->with('reportable')
            ->latest()
            ->limit(500)
            ->get()
            ->each(function (CommunityReport $report) use ($users) {
                $subject = $report->reportable;
                $userId = $subject instanceof Model ? ($subject->user_id ?? null) : null;

                if (! $userId) {
                    return;
                }

                $current = $users->get($userId, ['count' => 0, 'risk' => 0]);
                $users->put($userId, [
                    'count' => $current['count'] + 1,
                    'risk' => max($current['risk'], (int) $report->subject_ai_risk_score),
                ]);
            });

        $loadedUsers = User::query()
            ->whereIn('id', $users->keys())
            ->get(['id', 'name', 'email', 'community_trust_score', 'forum_reputation'])
            ->keyBy('id');

        return $users
            ->map(fn (array $data, int $userId) => [
                'user' => $loadedUsers->get($userId),
                'reports_count' => $data['count'],
                'max_risk' => $data['risk'],
            ])
            ->filter(fn (array $row) => filled($row['user']))
            ->sortByDesc('reports_count')
            ->take(8)
            ->values();
    }

    public function quickLinks(): array
    {
        return [
            ['label' => 'Forum Konulari', 'url' => url('/admin/forum-topics')],
            ['label' => 'Forum Cevaplari', 'url' => url('/admin/forum-posts')],
            ['label' => 'Canli Sohbet Moderasyonu', 'url' => url('/admin/live-chat-messages')],
            ['label' => 'Topluluk Raporlari', 'url' => url('/admin/community-reports')],
            ['label' => 'Kullanici Cezalari', 'url' => url('/admin/user-punishments')],
            ['label' => 'Canli Aktivite', 'url' => url('/admin/live-activities')],
        ];
    }

    private function highRiskCount(): int
    {
        return ForumTopic::query()->where('ai_risk_score', '>=', 70)->count()
            + ForumPost::query()->where('ai_risk_score', '>=', 70)->count()
            + LiveChatMessage::query()->where('ai_risk_score', '>=', 70)->count();
    }

    private function contentRow(string $type, Model $record, string $title, string $url, ?string $userName): array
    {
        return [
            'type' => $type,
            'title' => $title,
            'user' => $userName ?? 'Sistem',
            'status' => $record->status ?? '-',
            'risk_score' => (int) ($record->ai_risk_score ?? 0),
            'risk_label' => $record->ai_risk_label ?? 'low',
            'created_at' => $record->created_at,
            'url' => $url,
        ];
    }
}
