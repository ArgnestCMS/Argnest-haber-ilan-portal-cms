<?php

namespace App\Filament\Resources\ForumPosts\Tables;

use App\Helpers\NotificationHelper;
use App\Support\ForumGamification;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ForumPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('topic.title')
                    ->label('Konu')
                    ->limit(45)
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->default('Sistem')
                    ->sortable(),

                TextColumn::make('content')
                    ->label('Cevap')
                    ->html()
                    ->limit(80)
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

                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
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
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'approved',
                            'moderated_by' => auth()->id(),
                            'moderated_at' => now(),
                        ]);

                        $record->topic?->update([
                            'last_post_at' => $record->created_at,
                            'last_post_user_id' => $record->user_id,
                        ]);

                        if ($record->user) {
                            ForumGamification::award($record->user, 'post_approved', $record, [
                                'moderator_id' => auth()->id(),
                                'topic_id' => $record->forum_topic_id,
                            ]);
                        }

                        if ($record->user_id) {
                            NotificationHelper::sendToUser(
                                userId: $record->user_id,
                                type: 'forum_post_approved',
                                title: 'Forum cevabiniz onaylandi',
                                message: '"' . ($record->topic?->title ?? 'Forum konusu') . '" konusundaki cevabiniz yayina alindi.',
                                url: $record->topic?->status === 'published' ? route('forum.topics.show', $record->topic->slug) : route('forum.dashboard'),
                                data: [
                                    'topic_id' => $record->forum_topic_id,
                                    'post_id' => $record->id,
                                ]
                            );
                        }
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

                        if ($record->user) {
                            ForumGamification::award($record->user, 'content_rejected', $record, [
                                'moderator_id' => auth()->id(),
                                'topic_id' => $record->forum_topic_id,
                            ]);
                        }

                        if ($record->user_id) {
                            NotificationHelper::sendToUser(
                                userId: $record->user_id,
                                type: 'forum_post_rejected',
                                title: 'Forum cevabiniz reddedildi',
                                message: '"' . ($record->topic?->title ?? 'Forum konusu') . '" konusundaki cevabiniz moderasyon tarafindan reddedildi.',
                                url: route('forum.dashboard'),
                                data: [
                                    'topic_id' => $record->forum_topic_id,
                                    'post_id' => $record->id,
                                ]
                            );
                        }
                    }),

                DeleteAction::make()->label('Sil'),
                RestoreAction::make()->label('Geri Yükle'),
                ForceDeleteAction::make()->label('Kalıcı Sil'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Seçilenleri Sil'),
                ]),
            ]);
    }
}
