<?php

namespace App\Filament\Resources\SiteAnnouncements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiteAnnouncementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),
                TextColumn::make('icon')
                    ->label('İkon')
                    ->placeholder('-'),
                TextColumn::make('text')
                    ->label('Duyuru')
                    ->limit(70)
                    ->searchable(),
                TextColumn::make('link_url')
                    ->label('Link')
                    ->limit(40)
                    ->placeholder('-'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('starts_at')
                    ->label('Başlangıç')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('ends_at')
                    ->label('Bitiş')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
