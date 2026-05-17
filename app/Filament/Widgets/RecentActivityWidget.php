<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentActivityWidget extends TableWidget
{
    protected static ?string $heading = 'Son Sistem Aktiviteleri';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ActivityLog::query()
                    ->with('user')
                    ->latest()
                    ->limit(10)
            )
            ->defaultPaginationPageOption(10)
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->placeholder('Sistem')
                    ->searchable(),

                TextColumn::make('action')
                    ->label('İşlem')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'login' => 'Giriş',
                        'logout' => 'Çıkış',
                        'failed_login' => 'Başarısız Giriş',
                        'suspicious_login' => 'Şüpheli Giriş',
                        'suspicious_device_login' => 'Farklı Cihaz',

                        'create_news' => 'Haber Eklendi',
                        'edit_news' => 'Haber Düzenlendi',
                        'delete_news' => 'Haber Silindi',

                        'create_announcement' => 'İlan Eklendi',
                        'edit_announcement' => 'İlan Düzenlendi',
                        'delete_announcement' => 'İlan Silindi',

                        'comment_approved' => 'Yorum Onaylandı',
                        'comment_rejected' => 'Yorum Reddedildi',
                        'punishment_given' => 'Ceza Verildi',

                        default => $state ?? 'Bilinmiyor',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'login',
                        'create_news',
                        'create_announcement',
                        'comment_approved' => 'success',

                        'logout' => 'gray',

                        'edit_news',
                        'edit_announcement',
                        'punishment_given',
                        'suspicious_device_login' => 'warning',

                        'delete_news',
                        'delete_announcement',
                        'comment_rejected',
                        'failed_login',
                        'suspicious_login' => 'danger',

                        default => 'gray',
                    }),

                TextColumn::make('description')
                    ->label('Açıklama')
                    ->limit(80)
                    ->wrap(),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->copyable(),

                TextColumn::make('device')
                    ->label('Cihaz')
                    ->badge(),

                TextColumn::make('browser')
                    ->label('Tarayıcı')
                    ->badge(),
            ]);
    }

    public static function canView(): bool
{
    return auth()->user()?->isAdmin()
        || auth()->user()?->hasPermission('kullanici_yonet');
}
    }