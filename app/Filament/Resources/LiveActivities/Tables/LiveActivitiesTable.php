<?php

namespace App\Filament\Resources\LiveActivities\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class LiveActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('occurred_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->limit(45)
                    ->wrap(),

                TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->default('Sistem')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('source')
                    ->label('Kaynak')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'auth' => 'Oturum',
                        'forum' => 'Forum',
                        'chat' => 'Sohbet',
                        'system' => 'Sistem',
                        default => $state ?? 'Bilinmiyor',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'auth' => 'info',
                        'forum' => 'danger',
                        'chat' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'user_login' => 'Giriş',
                        'user_logout' => 'Çıkış',
                        'forum_topic_created' => 'Yeni Konu',
                        'forum_post_created' => 'Yeni Cevap',
                        'live_chat_message' => 'Sohbet Mesajı',
                        default => $state ?? 'Bilinmiyor',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('severity')
                    ->label('Seviye')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'info' => 'Bilgi',
                        'success' => 'Aktif',
                        'warning' => 'Uyarı',
                        'danger' => 'Kritik',
                        default => $state ?? 'Bilgi',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'success' => 'success',
                        'warning' => 'warning',
                        'danger' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                IconColumn::make('is_public')
                    ->label('Akışta')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_important')
                    ->label('Önemli')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('occurred_at')
                    ->label('Aktivite Zamanı')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tip')
                    ->options([
                        'user_login' => 'Kullanıcı Girişi',
                        'user_logout' => 'Kullanıcı Çıkışı',
                        'forum_topic_created' => 'Yeni Forum Konusu',
                        'forum_post_created' => 'Yeni Forum Cevabı',
                        'live_chat_message' => 'Canlı Sohbet Mesajı',
                    ]),

                SelectFilter::make('source')
                    ->label('Kaynak')
                    ->options([
                        'auth' => 'Oturum',
                        'forum' => 'Forum',
                        'chat' => 'Sohbet',
                        'system' => 'Sistem',
                    ]),

                SelectFilter::make('severity')
                    ->label('Seviye')
                    ->options([
                        'info' => 'Bilgi',
                        'success' => 'Aktif',
                        'warning' => 'Uyarı',
                        'danger' => 'Kritik',
                    ]),

                TernaryFilter::make('is_public')
                    ->label('Akışta Görünür'),

                TernaryFilter::make('is_important')
                    ->label('Önemli'),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()->label('Detay'),
                EditAction::make()->label('Düzenle'),

                Action::make('mark_important')
                    ->label('Önemli')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->visible(fn ($record) => ! $record->is_important && ! $record->trashed())
                    ->action(fn ($record) => $record->update(['is_important' => true])),

                Action::make('unmark_important')
                    ->label('Önem Kaldır')
                    ->icon('heroicon-o-star')
                    ->color('gray')
                    ->visible(fn ($record) => $record->is_important && ! $record->trashed())
                    ->action(fn ($record) => $record->update(['is_important' => false])),

                Action::make('hide')
                    ->label('Gizle')
                    ->icon('heroicon-o-eye-slash')
                    ->color('warning')
                    ->visible(fn ($record) => $record->is_public && ! $record->trashed())
                    ->action(fn ($record) => $record->update(['is_public' => false])),

                Action::make('show')
                    ->label('Yayınla')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->visible(fn ($record) => ! $record->is_public && ! $record->trashed())
                    ->action(fn ($record) => $record->update(['is_public' => true])),

                DeleteAction::make()->label('Sil'),
                RestoreAction::make()->label('Geri Al'),
                ForceDeleteAction::make()->label('Kalıcı Sil'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('hide_selected')
                        ->label('Seçilenleri Gizle')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['is_public' => false])),

                    BulkAction::make('show_selected')
                        ->label('Seçilenleri Yayınla')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_public' => true])),

                    BulkAction::make('important_selected')
                        ->label('Önemli İşaretle')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['is_important' => true])),

                    DeleteBulkAction::make()->label('Seçilenleri Sil'),
                    RestoreBulkAction::make()->label('Seçilenleri Geri Al'),
                    ForceDeleteBulkAction::make()->label('Kalıcı Sil'),
                ]),
            ]);
    }
}
