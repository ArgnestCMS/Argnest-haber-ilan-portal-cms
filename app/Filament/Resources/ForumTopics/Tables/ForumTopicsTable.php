<?php

namespace App\Filament\Resources\ForumTopics\Tables;

use App\Helpers\NotificationHelper;
use App\Models\ForumCategory;
use App\Support\ForumGamification;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ForumTopicsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->default('Sistem')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Bekliyor',
                        'published' => 'Yayında',
                        'hidden' => 'Gizli',
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

                IconColumn::make('is_pinned')
                    ->label('Sabit')
                    ->boolean(),

                IconColumn::make('is_locked')
                    ->label('Kilitli')
                    ->boolean(),

                IconColumn::make('is_solved')
                    ->label('Çözüldü')
                    ->boolean(),

                IconColumn::make('replies_closed')
                    ->label('Cevap Kapali')
                    ->boolean(),

                TextColumn::make('slow_mode_seconds')
                    ->label('Yavas Mod')
                    ->suffix(' sn')
                    ->sortable(),

                TextColumn::make('lastPostUser.name')
                    ->label('Son Cevaplayan')
                    ->default('-'),

                TextColumn::make('posts_count')
                    ->label('Cevap')
                    ->counts('posts')
                    ->sortable(),

                TextColumn::make('views')
                    ->label('Okunma')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Bekliyor',
                        'published' => 'Yayında',
                        'hidden' => 'Gizli',
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
                Action::make('publish')
                    ->label('Yayınla')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'published')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'published',
                            'last_post_at' => $record->last_post_at ?: now(),
                            'last_post_user_id' => $record->last_post_user_id ?: $record->user_id,
                        ]);

                        if ($record->user) {
                            ForumGamification::award($record->user, 'topic_approved', $record, [
                                'moderator_id' => auth()->id(),
                            ]);
                        }

                        if ($record->user_id) {
                            NotificationHelper::sendToUser(
                                userId: $record->user_id,
                                type: 'forum_topic_approved',
                                title: 'Forum konunuz onaylandi',
                                message: '"' . $record->title . '" baslikli forum konunuz yayina alindi.',
                                url: route('forum.topics.show', $record->slug),
                                data: ['topic_id' => $record->id]
                            );
                        }
                    }),
                Action::make('hide')
                    ->label('Gizle')
                    ->icon('heroicon-o-eye-slash')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status !== 'hidden')
                    ->action(function ($record) {
                        $record->update(['status' => 'hidden']);

                        if ($record->user_id) {
                            NotificationHelper::sendToUser(
                                userId: $record->user_id,
                                type: 'forum_topic_hidden',
                                title: 'Forum konunuz gizlendi',
                                message: '"' . $record->title . '" baslikli forum konunuz moderasyon tarafindan gizlendi.',
                                url: route('forum.dashboard'),
                                data: ['topic_id' => $record->id]
                            );
                        }
                    }),
                Action::make('toggle_lock')
                    ->label(fn ($record) => $record->is_locked ? 'Kilidi Ac' : 'Kilitle')
                    ->icon(fn ($record) => $record->is_locked ? 'heroicon-o-lock-open' : 'heroicon-o-lock-closed')
                    ->color(fn ($record) => $record->is_locked ? 'success' : 'warning')
                    ->action(fn ($record) => $record->update(['is_locked' => ! $record->is_locked])),
                Action::make('toggle_pin')
                    ->label(fn ($record) => $record->is_pinned ? 'Sabiti Kaldir' : 'Sabitle')
                    ->icon('heroicon-o-bookmark')
                    ->color('info')
                    ->action(fn ($record) => $record->update(['is_pinned' => ! $record->is_pinned])),
                Action::make('toggle_solved')
                    ->label(fn ($record) => $record->is_solved ? 'Cozumu Kaldir' : 'Cozuldu')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->action(function ($record) {
                        $wasSolved = (bool) $record->is_solved;

                        $record->update(['is_solved' => ! $wasSolved]);

                        if (! $wasSolved && $record->user) {
                            ForumGamification::award($record->user, 'topic_solved', $record, [
                                'moderator_id' => auth()->id(),
                            ]);
                        }
                    }),
                Action::make('toggle_replies')
                    ->label(fn ($record) => $record->replies_closed ? 'Cevaplari Ac' : 'Cevaplari Kapat')
                    ->icon(fn ($record) => $record->replies_closed ? 'heroicon-o-chat-bubble-left-right' : 'heroicon-o-no-symbol')
                    ->color(fn ($record) => $record->replies_closed ? 'success' : 'warning')
                    ->action(fn ($record) => $record->update(['replies_closed' => ! $record->replies_closed])),
                Action::make('move')
                    ->label('Tasi')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('gray')
                    ->form([
                        Select::make('forum_category_id')
                            ->label('Yeni Kategori')
                            ->options(fn () => ForumCategory::query()
                                ->where('is_active', true)
                                ->orderBy('sort_order')
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->required(),
                    ])
                    ->action(fn ($record, array $data) => $record->update([
                        'forum_category_id' => $data['forum_category_id'],
                    ])),
                Action::make('slow_mode')
                    ->label('Yavas Mod')
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->form([
                        TextInput::make('slow_mode_seconds')
                            ->label('Saniye')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(3600)
                            ->default(fn ($record) => $record->slow_mode_seconds ?? 0)
                            ->required(),
                    ])
                    ->action(fn ($record, array $data) => $record->update([
                        'slow_mode_seconds' => (int) $data['slow_mode_seconds'],
                    ])),
                Action::make('moderator_note')
                    ->label('Moderator Notu')
                    ->icon('heroicon-o-pencil-square')
                    ->color('gray')
                    ->form([
                        Textarea::make('moderator_note')
                            ->label('Not')
                            ->default(fn ($record) => $record->moderator_note)
                            ->rows(5)
                            ->maxLength(5000),
                    ])
                    ->action(fn ($record, array $data) => $record->update([
                        'moderator_note' => $data['moderator_note'] ?? null,
                    ])),
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
