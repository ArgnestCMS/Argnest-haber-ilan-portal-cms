<?php

namespace App\Filament\Resources\ForumTopics\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
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

                IconColumn::make('is_pinned')
                    ->label('Sabit')
                    ->boolean(),

                IconColumn::make('is_locked')
                    ->label('Kilitli')
                    ->boolean(),

                IconColumn::make('is_solved')
                    ->label('Çözüldü')
                    ->boolean(),

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

                        $record->user?->addForumReputation(5);
                    }),
                Action::make('hide')
                    ->label('Gizle')
                    ->icon('heroicon-o-eye-slash')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status !== 'hidden')
                    ->action(fn ($record) => $record->update(['status' => 'hidden'])),
                Action::make('mark_solved')
                    ->label('Çözüldü')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => ! $record->is_solved)
                    ->action(fn ($record) => $record->update(['is_solved' => true])),
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
