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
                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable(),

                TextColumn::make('slug')
                    ->label('URL')
                    ->searchable(),

                ImageColumn::make('image')
                    ->label('Görsel'),

                IconColumn::make('is_headline')
                    ->label('Manşet')
                    ->boolean(),

                IconColumn::make('comments_enabled')
                    ->label('Yorum')
                    ->boolean(),

                TextColumn::make('views')
                    ->label('Okunma')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Oluşturma')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Güncelleme')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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