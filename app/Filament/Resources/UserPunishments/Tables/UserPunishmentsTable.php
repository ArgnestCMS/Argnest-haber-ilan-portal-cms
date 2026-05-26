<?php

namespace App\Filament\Resources\UserPunishments\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class UserPunishmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')

            ->columns([

                TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('moderator.name')
                    ->label('İşlemi Yapan')
                    ->default('-')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Ceza Türü')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'warning' => 'Uyarı',
                        'mute' => 'Yorum Susturma',
                        'temporary_ban' => 'Süreli Ban',
                        'permanent_ban' => 'Süresiz Ban',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'warning' => 'warning',
                        'mute' => 'info',
                        'temporary_ban' => 'danger',
                        'permanent_ban' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('reason')
                    ->label('Sebep')
                    ->limit(80)
                    ->wrap()
                    ->searchable(),

                TextColumn::make('expires_at')
    ->label('Bitiş Tarihi')
    ->formatStateUsing(fn ($state) => $state ? $state->format('d.m.Y H:i') : 'Süresiz')
    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Verilme Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

            ])

            ->filters([

                SelectFilter::make('type')
                    ->label('Ceza Türü')
                    ->options([
                        'warning' => 'Uyarı',
                        'mute' => 'Yorum Susturma',
                        'temporary_ban' => 'Süreli Ban',
                        'permanent_ban' => 'Süresiz Ban',
                    ]),

                SelectFilter::make('is_active')
                    ->label('Durum')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Pasif',
                    ]),

            ])

            ->recordActions([

                ViewAction::make()
                    ->label('Görüntüle'),

                EditAction::make()
                    ->label('Düzenle'),

                Action::make('deactivate')
                    ->label('Cezayı Kaldır')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->is_active)
                    ->requiresConfirmation()
                    ->modalHeading('Cezayı kaldır')
                    ->modalDescription('Bu kullanıcının aktif cezası pasif hale getirilecek.')
                    ->action(function ($record) {
                        $record->update([
                            'is_active' => false,
                        ]);
                    }),

                Action::make('activate')
                    ->label('Tekrar Aktif Et')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning')
                    ->visible(fn ($record) => ! $record->is_active)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'is_active' => true,
                        ]);
                    }),

                DeleteAction::make()
                    ->label('Sil'),

            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Seçilenleri Sil'),
                ]),
            ]);
    }
}