<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class SecurityAlertWidget extends TableWidget
{
    protected static ?string $heading = 'Güvenlik Uyarıları';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ActivityLog::query()
                    ->whereIn('action', [
                        'failed_login',
                        'suspicious_login',
                        'suspicious_device_login',
                    ])
                    ->latest()
                    ->limit(10)
            )
            ->defaultPaginationPageOption(10)
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('action')
                    ->label('Uyarı Tipi')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'failed_login' => 'Başarısız Giriş',
                        'suspicious_login' => 'Şüpheli Giriş',
                        'suspicious_device_login' => 'Farklı Cihaz Girişi',
                        default => $state ?? 'Bilinmiyor',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'failed_login',
                        'suspicious_login' => 'danger',
                        'suspicious_device_login' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('description')
                    ->label('Açıklama')
                    ->limit(70)
                    ->wrap(),

                TextColumn::make('properties.email')
                    ->label('Email')
                    ->placeholder('-')
                    ->copyable(),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->copyable()
                    ->placeholder('-'),

                TextColumn::make('device')
                    ->label('Cihaz')
                    ->badge()
                    ->placeholder('-'),

                TextColumn::make('browser')
                    ->label('Tarayıcı')
                    ->badge()
                    ->placeholder('-'),

                TextColumn::make('platform')
                    ->label('Platform')
                    ->badge()
                    ->placeholder('-'),
            ]);
    }

    public static function canView(): bool
{
    return auth()->user()?->isAdmin()
        || auth()->user()?->hasPermission('kullanici_yonet');
}
}