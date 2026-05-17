<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ContentCreationChartWidget extends ChartWidget
{
    protected ?string $heading = 'Son 7 Gün İçerik Üretimi';

    protected static ?int $sort = 21;

    protected function getData(): array
    {
        $days = collect(range(6, 0))->map(fn ($day) => now()->subDays($day)->format('Y-m-d'))
            ->push(now()->format('Y-m-d'));

        return [
            'datasets' => [
                [
                    'label' => 'Haber',
                    'data' => $this->countByAction($days, 'create_news'),
                ],
                [
                    'label' => 'İlan',
                    'data' => $this->countByAction($days, 'create_announcement'),
                ],
                [
                    'label' => 'Reklam',
                    'data' => $this->countByAction($days, 'create_advertisement'),
                ],
                [
                    'label' => 'Kategori',
                    'data' => $this->countByAction($days, 'create_category'),
                ],
            ],
            'labels' => $days->map(fn ($date) => Carbon::parse($date)->format('d.m'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function countByAction($days, string $action): array
    {
        return $days->map(fn ($date) =>
            ActivityLog::query()
                ->where('action', $action)
                ->whereDate('created_at', $date)
                ->count()
        )->toArray();
    }

    public static function canView(): bool
{
    return auth()->user()?->isAdmin()
    || auth()->user()?->hasPermission('haber_gor')
    || auth()->user()?->hasPermission('ilan_gor')
    || auth()->user()?->hasPermission('reklam_yonet');
}
}