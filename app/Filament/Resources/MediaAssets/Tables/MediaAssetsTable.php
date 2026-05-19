<?php

namespace App\Filament\Resources\MediaAssets\Tables;

use App\Models\MediaAsset;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class MediaAssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('thumbnail_url')
                    ->label('Onizleme')
                    ->square()
                    ->defaultImageUrl(fn (MediaAsset $record) => $record->url),

                TextColumn::make('original_name')
                    ->label('Dosya')
                    ->searchable()
                    ->limit(34)
                    ->wrap(),

                TextColumn::make('owner.name')
                    ->label('Kullanici')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Sistem'),

                TextColumn::make('collection')
                    ->label('Koleksiyon')
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'ready' => 'Hazir',
                        'suspicious' => 'Supheli',
                        'blocked' => 'Engelli',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'suspicious' => 'warning',
                        'blocked' => 'danger',
                        'ready' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('mime_type')
                    ->label('Mime')
                    ->badge()
                    ->searchable(),

                TextColumn::make('human_size')
                    ->label('Boyut')
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderBy('size', $direction)),

                IconColumn::make('is_large')
                    ->label('Buyuk')
                    ->boolean(),

                IconColumn::make('is_orphan')
                    ->label('Orphan')
                    ->boolean(),

                IconColumn::make('thumbnail_missing')
                    ->label('Thumb Eksik')
                    ->boolean(),

                IconColumn::make('storage_missing')
                    ->label('Dosya Eksik')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Yukleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Kullanici')
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('collection')
                    ->label('Koleksiyon')
                    ->options([
                        'forum' => 'Forum',
                        'direct_message' => 'DM',
                    ]),

                SelectFilter::make('visibility')
                    ->label('Gorunurluk')
                    ->options([
                        'public' => 'Public',
                        'private' => 'Private',
                    ]),

                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'ready' => 'Hazir',
                        'suspicious' => 'Supheli',
                        'blocked' => 'Engelli',
                    ]),

                SelectFilter::make('mime_type')
                    ->label('Mime')
                    ->options([
                        'image/jpeg' => 'JPEG',
                        'image/png' => 'PNG',
                        'image/webp' => 'WEBP',
                        'image/gif' => 'GIF',
                    ]),

                Filter::make('orphan')
                    ->label('Orphan Medya')
                    ->query(fn (Builder $query): Builder => $query->orphan()),

                Filter::make('large')
                    ->label('Buyuk Medya')
                    ->query(fn (Builder $query): Builder => $query->where('size', '>=', (int) config('media.management.large_file_warning_mb', 20) * 1024 * 1024)),

                Filter::make('missing_thumbnail')
                    ->label('Thumbnail Yolu Var')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('thumbnail_path')),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()->label('Detay'),

                Action::make('mark_suspicious')
                    ->label('Supheli Isaretle')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning')
                    ->visible(fn (MediaAsset $record) => $record->status !== 'suspicious')
                    ->form([
                        Textarea::make('note')
                            ->label('Moderator Notu')
                            ->rows(4)
                            ->maxLength(1000),
                    ])
                    ->action(fn (MediaAsset $record, array $data) => $record->markSuspicious($data['note'] ?? null)),

                Action::make('mark_ready')
                    ->label('Hazir Yap')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (MediaAsset $record) => $record->status !== 'ready')
                    ->action(fn (MediaAsset $record) => $record->markReady()),

                Action::make('block')
                    ->label('Engelle')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn (MediaAsset $record) => $record->status !== 'blocked')
                    ->requiresConfirmation()
                    ->action(fn (MediaAsset $record) => $record->update([
                        'status' => 'blocked',
                        'metadata' => array_merge($record->metadata ?? [], [
                            'blocked_at' => now()->toISOString(),
                            'blocked_by' => auth()->id(),
                        ]),
                    ])),

                DeleteAction::make()
                    ->label('Soft Delete')
                    ->requiresConfirmation(),

                RestoreAction::make()
                    ->label('Geri Al'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_suspicious')
                        ->label('Secilenleri Supheli Isaretle')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->color('warning')
                        ->action(fn (Collection $records) => $records->each->markSuspicious('Toplu isaretleme')),

                    BulkAction::make('soft_delete_orphans')
                        ->label('Secili Orphanlari Soft Delete')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records
                            ->filter(fn (MediaAsset $record) => $record->is_orphan)
                            ->each->delete()),

                    DeleteBulkAction::make()
                        ->label('Secilenleri Soft Delete'),
                ]),
            ]);
    }
}
