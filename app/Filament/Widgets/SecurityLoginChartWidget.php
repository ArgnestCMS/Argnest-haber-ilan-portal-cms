<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SecurityLoginChartWidget extends ChartWidget
{
    protected ?string $heading = 'Son 7 Gün Giriş Güvenliği';

    protected static ?int $sort = 20;

    protected function getData(): array
    {
        $days = collect(range(6, 0))->map(function ($day) {
            return now()->subDays($day)->format('Y-m-d');
        })->push(now()->format('Y-m-d'));

        return [
            'datasets' => [
                [
                    'label' => 'Başarılı Giriş',
                    'data' => $this->countByAction($days, 'login'),
                ],
                [
                    'label' => 'Başarısız Giriş',
                    'data' => $this->countByAction($days, 'failed_login'),
                ],
                [
                    'label' => 'Şüpheli Giriş',
                    'data' => $this->countByAction($days, 'suspicious_login'),
                ],
            ],
            'labels' => $days->map(fn ($date) => Carbon::parse($date)->format('d.m'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function countByAction($days, string $action): array
    {
        return $days->map(function ($date) use ($action) {
            return ActivityLog::query()
                ->where('action', $action)
                ->whereDate('created_at', $date)
                ->count();
        })->toArray();
    }

    public static function canView(): bool
{
    return auth()->user()?->isAdmin()
        || auth()->user()?->hasPermission('kullanici_yonet');
}
}