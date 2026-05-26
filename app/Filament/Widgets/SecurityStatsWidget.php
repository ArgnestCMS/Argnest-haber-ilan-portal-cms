<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SecurityStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [

            Stat::make(
                'Bugünkü Girişler',
                ActivityLog::query()
                    ->where('action', 'login')
                    ->whereDate('created_at', today())
                    ->count()
            )
                ->description('Bugün yapılan başarılı girişler')
                ->descriptionIcon('heroicon-m-arrow-right-end-on-rectangle')
                ->color('success'),

            Stat::make(
                'Başarısız Girişler',
                ActivityLog::query()
                    ->where('action', 'failed_login')
                    ->whereDate('created_at', today())
                    ->count()
            )
                ->description('Yanlış şifre denemeleri')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make(
                'Şüpheli Girişler',
                ActivityLog::query()
                    ->where('action', 'suspicious_login')
                    ->whereDate('created_at', today())
                    ->count()
            )
                ->description('Rate limit tetiklenen girişler')
                ->descriptionIcon('heroicon-m-shield-exclamation')
                ->color('warning'),

            Stat::make(
                'Farklı Cihaz',
                ActivityLog::query()
                    ->where('action', 'suspicious_device_login')
                    ->whereDate('created_at', today())
                    ->count()
            )
                ->description('Yeni cihaz/IP girişleri')
                ->descriptionIcon('heroicon-m-computer-desktop')
                ->color('info'),

        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('kullanici_yonet');
    }
}