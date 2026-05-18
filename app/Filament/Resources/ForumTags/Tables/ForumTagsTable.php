<?php

namespace App\Filament\Resources\ForumTags\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ForumTagsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('name')
                    ->label('Etiket')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('URL')
                    ->searchable(),

                TextColumn::make('color')
                    ->label('Renk'),

                TextColumn::make('topics_count')
                    ->label('Konu')
                    ->counts('topics')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label('Siralama')
                    ->numeric()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()->label('Goruntule'),
                EditAction::make()->label('Duzenle'),
                DeleteAction::make()->label('Sil'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Secilenleri Sil'),
                ]),
            ]);
    }
}
