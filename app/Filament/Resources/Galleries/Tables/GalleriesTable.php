<?php

namespace App\Filament\Resources\Galleries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GalleriesTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('created_at', 'desc')

            ->columns([

                ImageColumn::make('cover_image')
                    ->label('Kapak')
                    ->square(),

                TextColumn::make('title')
                    ->label('Galeri Başlığı')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->placeholder('-'),

                TextColumn::make('images_count')
                    ->label('Resim Sayısı')
                    ->counts('images')
                    ->badge()
                    ->color('info'),

                TextColumn::make('views')
                    ->label('Görüntülenme')
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->label('Öne Çıkan')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Durum')
                    ->boolean(),

                TextColumn::make('published_at')
                    ->label('Yayın Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

            ])

            ->filters([
                //
            ])

            ->recordActions([

                ViewAction::make(),

                EditAction::make(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ]);
    }
}