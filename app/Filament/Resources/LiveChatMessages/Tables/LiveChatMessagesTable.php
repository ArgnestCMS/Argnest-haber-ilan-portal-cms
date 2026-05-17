<?php

namespace App\Filament\Resources\LiveChatMessages\Tables;

use App\Models\UserPunishment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class LiveChatMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->default('Sistem')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('message')
                    ->label('Mesaj')
                    ->limit(90)
                    ->wrap()
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Bekliyor',
                        'approved' => 'Onaylandı',
                        'rejected' => 'Reddedildi',
                        default => $state,
                    }),

                TextColumn::make('moderator.name')
                    ->label('Son Moderatör')
                    ->default('-'),

                TextColumn::make('moderated_at')
                    ->label('Son Moderasyon')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Gönderim')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Bekleyen Kuyruk',
                        'approved' => 'Onaylanan',
                        'rejected' => 'Reddedilen',
                    ]),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()->label('Görüntüle'),
                EditAction::make()->label('Düzenle'),

                Action::make('approve')
                    ->label('Onayla')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'approved')
                    ->action(fn ($record) => $record->update([
                        'status' => 'approved',
                        'moderated_by' => auth()->id(),
                        'moderated_at' => now(),
                        'moderation_note' => null,
                    ])),

                Action::make('reject')
                    ->label('Reddet')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status !== 'rejected')
                    ->form([
                        Textarea::make('moderation_note')
                            ->label('Red Sebebi')
                            ->rows(3),
                    ])
                    ->action(fn ($record, array $data) => $record->update([
                        'status' => 'rejected',
                        'moderated_by' => auth()->id(),
                        'moderated_at' => now(),
                        'moderation_note' => $data['moderation_note'] ?? null,
                    ])),

                Action::make('hide')
                    ->label('Gizle')
                    ->icon('heroicon-o-eye-slash')
                    ->color('warning')
                    ->visible(fn ($record) => ! $record->trashed())
                    ->action(fn ($record) => $record->delete()),

                Action::make('punish')
                    ->label('Ceza Ver')
                    ->icon('heroicon-o-no-symbol')
                    ->color('warning')
                    ->visible(fn ($record) => filled($record->user_id))
                    ->form([
                        Select::make('type')
                            ->label('Ceza Türü')
                            ->options([
                                'warning' => 'Uyarı',
                                'mute' => 'Sohbet Susturma',
                                'temporary_ban' => 'Süreli Ban',
                                'permanent_ban' => 'Süresiz Ban',
                            ])
                            ->default('mute')
                            ->required(),

                        Textarea::make('reason')
                            ->label('Ceza Sebebi')
                            ->rows(4)
                            ->required(),

                        DateTimePicker::make('expires_at')
                            ->label('Ceza Bitiş Tarihi')
                            ->helperText('Uyarı veya süresiz ban için boş bırakabilirsiniz.'),
                    ])
                    ->requiresConfirmation()
                    ->action(function ($record, array $data) {
                        UserPunishment::create([
                            'user_id' => $record->user_id,
                            'moderator_id' => auth()->id(),
                            'type' => $data['type'],
                            'reason' => $data['reason'],
                            'expires_at' => $data['expires_at'] ?? null,
                            'is_active' => true,
                        ]);

                        $record->update([
                            'status' => 'rejected',
                            'moderated_by' => auth()->id(),
                            'moderated_at' => now(),
                            'moderation_note' => 'Kullanıcıya ceza verildi: ' . $data['reason'],
                        ]);
                    }),

                DeleteAction::make()->label('Sil'),
                RestoreAction::make()->label('Geri Al'),
                ForceDeleteAction::make()->label('Kalıcı Sil'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Seçilenleri Sil'),
                ]),
            ]);
    }
}
