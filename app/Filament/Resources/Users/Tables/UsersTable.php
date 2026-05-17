<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')

            ->columns([

                ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular(),

                TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('E-Posta')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('roleModel.name')
                    ->label('Rol')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'warning' => 'suspended',
                        'danger' => 'banned',
                        'gray' => 'frozen',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active' => 'Aktif',
                        'suspended' => 'Askıda',
                        'banned' => 'Banlı',
                        'frozen' => 'Donduruldu',
                        default => $state,
                    }),

                IconColumn::make('is_active')
                    ->label('Hesap')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                IconColumn::make('email_verified_at')
                    ->label('Mail')
                    ->boolean()
                    ->getStateUsing(fn ($record) => filled($record->email_verified_at))
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('success')
                    ->falseColor('warning'),

                TextColumn::make('created_at')
                    ->label('Kayıt Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->filters([

                SelectFilter::make('role_id')
                    ->label('Rol')
                    ->relationship('roleModel', 'name'),

                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'active' => 'Aktif',
                        'suspended' => 'Askıda',
                        'banned' => 'Banlı',
                        'frozen' => 'Donduruldu',
                    ]),

                SelectFilter::make('is_active')
                    ->label('Hesap Aktifliği')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Pasif',
                    ]),

            ])

            ->recordActions([

                Action::make('verify_email')
                    ->label('Mail Doğrula')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => blank($record->email_verified_at))
                    ->requiresConfirmation()
                    ->action(function ($record) {

                        $record->update([
                            'email_verified_at' => now(),
                        ]);

                        Notification::make()
                            ->title('E-posta manuel doğrulandı.')
                            ->success()
                            ->send();

                    }),

                Action::make('activate')
                    ->label('Aktif Et')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => ! $record->is_active)
                    ->requiresConfirmation()
                    ->action(function ($record) {

                        $record->update([
                            'is_active' => true,
                            'status' => 'active',
                        ]);

                        Notification::make()
                            ->title('Kullanıcı aktif edildi.')
                            ->success()
                            ->send();

                    }),

                Action::make('deactivate')
                    ->label('Pasif Et')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->is_active)
                    ->requiresConfirmation()
                    ->action(function ($record) {

                        $record->update([
                            'is_active' => false,
                            'status' => 'frozen',
                        ]);

                        Notification::make()
                            ->title('Kullanıcı pasif edildi.')
                            ->warning()
                            ->send();

                    }),

                ViewAction::make()
                    ->label('Görüntüle'),

                EditAction::make()
                    ->label('Düzenle'),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make()
                        ->label('Seçilenleri Sil'),

                ]),

            ]);
    }
}