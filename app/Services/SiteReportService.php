<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\Comment;
use App\Models\ForumPost;
use App\Models\ForumTopic;
use App\Models\Gallery;
use App\Models\News;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class SiteReportService
{
    public function report(string $period = 'today', ?string $startDate = null, ?string $endDate = null): array
    {
        [$start, $end, $label] = $this->dateRange($period, $startDate, $endDate);

        return [
            'period' => [
                'key' => $period,
                'label' => $label,
                'start' => $start,
                'end' => $end,
            ],
            'metrics' => $this->metrics($start, $end),
            'top_news' => $this->topRows(News::query(), 'title', 'views'),
            'top_announcements' => $this->topRows(Announcement::query(), 'title', 'views'),
        ];
    }

    public function exportRows(array $report): array
    {
        $rows = [
            ['Site Raporu', $report['period']['label']],
            ['Baslangic', $report['period']['start']->format('d.m.Y H:i:s')],
            ['Bitis', $report['period']['end']->format('d.m.Y H:i:s')],
            [],
            ['Metrik', 'Deger'],
        ];

        foreach ($report['metrics'] as $metric) {
            $rows[] = [$metric['label'], $metric['value']];
        }

        $rows[] = [];
        $rows[] = ['En cok okunan haberler'];
        $rows[] = ['Baslik', 'Goruntulenme', 'Olusturma'];

        foreach ($report['top_news'] as $row) {
            $rows[] = [$row['title'], $row['views'], $row['created_at']];
        }

        $rows[] = [];
        $rows[] = ['En cok goruntulenen ilanlar'];
        $rows[] = ['Baslik', 'Goruntulenme', 'Olusturma'];

        foreach ($report['top_announcements'] as $row) {
            $rows[] = [$row['title'], $row['views'], $row['created_at']];
        }

        return $rows;
    }

    public function dateRange(string $period, ?string $startDate = null, ?string $endDate = null): array
    {
        return match ($period) {
            'week' => [now()->startOfWeek(), now()->endOfWeek(), 'Bu hafta'],
            'month' => [now()->startOfMonth(), now()->endOfMonth(), 'Bu ay'],
            'custom' => $this->customDateRange($startDate, $endDate),
            default => [today()->startOfDay(), today()->endOfDay(), 'Bugun'],
        };
    }

    private function customDateRange(?string $startDate, ?string $endDate): array
    {
        $start = filled($startDate) ? Carbon::parse($startDate)->startOfDay() : today()->startOfDay();
        $end = filled($endDate) ? Carbon::parse($endDate)->endOfDay() : today()->endOfDay();

        if ($end->lt($start)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [$start, $end, $start->format('d.m.Y') . ' - ' . $end->format('d.m.Y')];
    }

    private function metrics(Carbon $start, Carbon $end): array
    {
        return [
            ['label' => 'Toplam haber', 'value' => News::query()->count()],
            ['label' => 'Yeni haber', 'value' => $this->createdBetween(News::query(), $start, $end)->count()],
            ['label' => 'Toplam ilan', 'value' => Announcement::query()->count()],
            ['label' => 'Yeni ilan', 'value' => $this->createdBetween(Announcement::query(), $start, $end)->count()],
            ['label' => 'Toplam kullanici', 'value' => User::query()->count()],
            ['label' => 'Yeni kullanici', 'value' => $this->createdBetween(User::query(), $start, $end)->count()],
            ['label' => 'Toplam yorum', 'value' => Comment::query()->count()],
            ['label' => 'Bekleyen yorum', 'value' => Comment::query()->where('status', 'pending')->count()],
            ['label' => 'Onaylanan yorum', 'value' => Comment::query()->where('status', 'approved')->count()],
            ['label' => 'Forum konu sayisi', 'value' => ForumTopic::query()->count()],
            ['label' => 'Yeni forum konu', 'value' => $this->createdBetween(ForumTopic::query(), $start, $end)->count()],
            ['label' => 'Forum mesaj sayisi', 'value' => ForumPost::query()->count()],
            ['label' => 'Yeni forum mesaj', 'value' => $this->createdBetween(ForumPost::query(), $start, $end)->count()],
            ['label' => 'Galeri sayisi', 'value' => Gallery::query()->count()],
            ['label' => 'Yeni galeri', 'value' => $this->createdBetween(Gallery::query(), $start, $end)->count()],
            ['label' => 'Video sayisi', 'value' => Video::query()->count()],
            ['label' => 'Yeni video', 'value' => $this->createdBetween(Video::query(), $start, $end)->count()],
        ];
    }

    private function createdBetween(Builder $query, Carbon $start, Carbon $end): Builder
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    private function topRows(Builder $query, string $titleColumn, string $viewsColumn): array
    {
        return $query
            ->select([$titleColumn, $viewsColumn, 'created_at'])
            ->orderByDesc($viewsColumn)
            ->limit(10)
            ->get()
            ->map(fn ($record): array => [
                'title' => (string) $record->{$titleColumn},
                'views' => (int) $record->{$viewsColumn},
                'created_at' => $record->created_at?->format('d.m.Y H:i') ?? '-',
            ])
            ->all();
    }
}
