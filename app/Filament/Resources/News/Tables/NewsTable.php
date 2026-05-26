<?php

namespace App\Filament\Resources\News\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Görsel')
                    ->square()
                    ->toggleable(),

                TextColumn::make('title')
                    ->label('Başlık')
                    ->description(fn ($record) => $record->slug)
                    ->searchable()
                    ->sortable()
                    ->limit(70)
                    ->wrap(),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->searchable()
                    ->toggleable(),

                IconColumn::make('is_headline')
                    ->label('Manşet')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('comments_enabled')
                    ->label('Yorum')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('views')
                    ->label('Okunma')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Oluşturma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label('Görüntüle'),
                EditAction::make()->label('Düzenle'),
                DeleteAction::make()
                    ->label('Sil')
                    ->visible(fn () => auth()->user()?->role === 'admin'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Seçilenleri Sil'),
                ]),
            ]);
    }
}
