<?php

namespace App\Filament\Widgets;

use App\Models\Notification;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class NotificationCenterWidget extends TableWidget
{
    protected static ?string $heading = 'Bildirim Merkezi';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Notification::query()
                    ->where('user_id', auth()->id())
                    ->latest()
                    ->limit(10)
            )
            ->defaultPaginationPageOption(10)
            ->columns([
                IconColumn::make('is_read')
                    ->label('Durum')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-bell-alert')
                    ->trueColor('success')
                    ->falseColor('warning'),

                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->weight(fn ($record) => $record->is_read ? null : 'bold'),

                TextColumn::make('message')
                    ->label('Mesaj')
                    ->limit(80)
                    ->wrap(),

                TextColumn::make('type')
                    ->label('Tür')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'new_comment' => 'info',
                        'spam_comment' => 'danger',
                        'auto_punishment' => 'warning',
                        'security' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('read')
                    ->label('Aç')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('notifications.read', $record))
                    ->openUrlInNewTab(false),
            ]);
    }

    public static function canView(): bool
    {
        return auth()->check();
    }
}