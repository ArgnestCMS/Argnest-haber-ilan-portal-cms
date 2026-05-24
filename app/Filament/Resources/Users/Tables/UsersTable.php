<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

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

                TextColumn::make('deleted_at')
                    ->label('Silinme')
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

                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('verify_email')
                    ->label('Mail Doğrula')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (User $record): bool => ! $record->trashed() && blank($record->email_verified_at))
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
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
                    ->visible(fn (User $record): bool => ! $record->trashed() && ! $record->is_active)
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
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
                    ->visible(fn (User $record): bool => ! $record->trashed() && $record->is_active)
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
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
                    ->label('Düzenle')
                    ->visible(fn (User $record): bool => ! $record->trashed()),

                DeleteAction::make()
                    ->label('Sil')
                    ->modalHeading('Kullanıcıyı sil')
                    ->modalDescription('Kullanıcı çöp kutusuna taşınacak. İlişkili haber, yorum ve mesaj kayıtları korunur.')
                    ->successNotificationTitle('Kullanıcı silindi.')
                    ->visible(fn (User $record): bool => UserResource::canSafelyDelete($record))
                    ->requiresConfirmation(),

                Action::make('delete_blocked')
                    ->label('Sil')
                    ->icon('heroicon-o-trash')
                    ->color('gray')
                    ->visible(fn (User $record): bool => ! $record->trashed() && ! UserResource::canSafelyDelete($record))
                    ->disabled()
                    ->tooltip(fn (User $record): string => UserResource::deleteBlockReason($record)),

                RestoreAction::make()
                    ->label('Geri Yükle')
                    ->successNotificationTitle('Kullanıcı geri yüklendi.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('delete_selected')
                        ->label('Seçilenleri Sil')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Seçilen kullanıcıları sil')
                        ->modalDescription('Uygun kullanıcılar çöp kutusuna taşınacak. Kendi hesabınız ve son kalan admin silinmez.')
                        ->action(function (Collection $records): void {
                            $deleted = 0;
                            $skipped = 0;

                            $records->each(function (User $record) use (&$deleted, &$skipped): void {
                                if (! UserResource::canSafelyDelete($record)) {
                                    $skipped++;

                                    return;
                                }

                                $record->delete();
                                $deleted++;
                            });

                            Notification::make()
                                ->title($deleted > 0 ? 'Seçili kullanıcılar silindi.' : 'Silinecek uygun kullanıcı bulunamadı.')
                                ->body($skipped > 0 ? "{$skipped} kullanıcı güvenlik kuralları nedeniyle atlandı." : null)
                                ->status($deleted > 0 ? 'success' : 'warning')
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
