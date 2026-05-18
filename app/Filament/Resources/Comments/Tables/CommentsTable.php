<?php

namespace App\Filament\Resources\Comments\Tables;

use App\Helpers\ActivityLogger;
use App\Models\Notification;
use App\Models\UserPunishment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CommentsTable
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

                TextColumn::make('commentable_type')
                    ->label('Tür')
                    ->formatStateUsing(fn ($state) =>
                        str_contains($state, 'News') ? 'Haber' : 'İlan'
                    )
                    ->badge()
                    ->color('info'),

                TextColumn::make('content')
                    ->label('Yorum')
                    ->limit(80)
                    ->wrap()
                    ->searchable(),

                BadgeColumn::make('status')
                    ->label('Durum')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Bekliyor',
                        'approved' => 'Onaylandı',
                        'rejected' => 'Reddedildi',
                        default => $state,
                    }),

                TextColumn::make('ai_risk_label')
                    ->label('AI Risk')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'critical' => 'Kritik',
                        'high' => 'Yuksek',
                        'medium' => 'Orta',
                        'low' => 'Dusuk',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'critical' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('ai_risk_score')
                    ->label('Risk Puan')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('moderator.name')
                    ->label('Moderatör')
                    ->default('-'),

                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                IconColumn::make('is_edited')
                    ->label('Düzenlendi')
                    ->boolean(),

            ])

            ->filters([

                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Bekleyen',
                        'approved' => 'Onaylanan',
                        'rejected' => 'Reddedilen',
                    ]),

                SelectFilter::make('ai_review_required')
                    ->label('AI Kuyrugu')
                    ->options([
                        1 => 'Supheli Icerik',
                        0 => 'Normal',
                    ]),

            ])

            ->recordActions([

                ViewAction::make()
                    ->label('Görüntüle'),

                Action::make('approve')
                    ->label('Onayla')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'approved')
                    ->action(function ($record) {

                        $record->update([
                            'status' => 'approved',
                            'moderated_by' => auth()->id(),
                            'moderated_at' => now(),
                        ]);

                        $existingNotification = Notification::query()
    ->where('user_id', $record->user_id)
    ->where('type', 'comment_approved')
    ->where('is_read', false)
    ->latest()
    ->first();

if (! $existingNotification) {

    Notification::create([
        'user_id' => $record->user_id,
        'type' => 'comment_approved',
        'title' => 'Yorumunuz Onaylandı',
        'message' => 'Gönderdiğiniz yorum moderatör tarafından onaylandı ve yayına alındı.',
        'url' => '/dashboard',
        'is_read' => false,
    ]);

}

                        ActivityLogger::log(
                            'Yorum onaylandı',
                            auth()->user()->name . ' bir yorumu onayladı.',
                            [
                                'comment_id' => $record->id,
                                'comment_user_id' => $record->user_id,
                                'comment_content' => $record->content,
                                'status' => 'approved',
                            ]
                        );

                    }),

                Action::make('reject')
                    ->label('Reddet')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status !== 'rejected')
                    ->action(function ($record) {

                        $record->update([
                            'status' => 'rejected',
                            'moderated_by' => auth()->id(),
                            'moderated_at' => now(),
                        ]);

                        ActivityLogger::log(
                            'Yorum reddedildi',
                            auth()->user()->name . ' bir yorumu reddetti.',
                            [
                                'comment_id' => $record->id,
                                'comment_user_id' => $record->user_id,
                                'comment_content' => $record->content,
                                'status' => 'rejected',
                            ]
                        );

                    }),

                Action::make('punish')
                    ->label('Ceza Ver')
                    ->icon('heroicon-o-no-symbol')
                    ->color('warning')
                    ->modalHeading('Kullanıcıya Ceza Ver')
                    ->modalDescription('Bu yorumun sahibine uyarı, yorum susturma veya ban cezası verebilirsiniz.')
                    ->form([

                        Select::make('type')
                            ->label('Ceza Türü')
                            ->options([
                                'warning' => 'Uyarı',
                                'mute' => 'Yorum Susturma',
                                'temporary_ban' => 'Süreli Ban',
                                'permanent_ban' => 'Süresiz Ban',
                            ])
                            ->required(),

                        Textarea::make('reason')
                            ->label('Ceza Sebebi')
                            ->placeholder('Küfür, argo, hakaret, spam veya topluluk kurallarına aykırı davranış sebebini yazın...')
                            ->rows(5)
                            ->required(),

                        DateTimePicker::make('expires_at')
                            ->label('Ceza Bitiş Tarihi')
                            ->helperText('Süresiz ban veya uyarı için boş bırakabilirsiniz.'),

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

                        ActivityLogger::log(
                            'Kullanıcıya ceza verildi',
                            auth()->user()->name . ' kullanıcıya ceza verdi. Tür: ' . $data['type'],
                            [
                                'comment_id' => $record->id,
                                'comment_user_id' => $record->user_id,
                                'comment_content' => $record->content,
                                'punished_user_id' => $record->user_id,
                                'punishment_type' => $data['type'],
                                'reason' => $data['reason'],
                            ]
                        );

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
