<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminWelcomeWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -100;

    protected function getStats(): array
    {
        $user = auth()->user();

        return [

            Stat::make(
                'Hoş Geldiniz',
                $user?->name ?? 'Yetkili'
            )
                ->description(
                    'Rol: ' . ($user?->roleModel?->name ?? 'Kullanıcı')
                )
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('success'),

        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('panel_giris');
    }
}