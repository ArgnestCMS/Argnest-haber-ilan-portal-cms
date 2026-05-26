<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use App\Models\UserPunishment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ModerationStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [

            Stat::make(
                'Bekleyen Yorumlar',
                Comment::query()
                    ->where('status', 'pending')
                    ->count()
            )
                ->description('Moderasyon bekleyen yorumlar')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(
                'Onaylanan Yorumlar',
                Comment::query()
                    ->where('status', 'approved')
                    ->count()
            )
                ->description('Yayındaki yorumlar')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make(
                'Reddedilen Yorumlar',
                Comment::query()
                    ->where('status', 'rejected')
                    ->count()
            )
                ->description('Reddedilmiş yorumlar')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make(
                'Aktif Cezalar',
                UserPunishment::query()
                    ->where('is_active', true)
                    ->count()
            )
                ->description('Aktif kullanıcı cezaları')
                ->descriptionIcon('heroicon-m-shield-exclamation')
                ->color('danger'),

        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('yorum_moderasyonu')
            || auth()->user()?->hasPermission('forum_moderasyonu');
    }
}