<?php

namespace App\Filament\Resources\HeaderSlots\Tables;

use App\Models\HeaderSlot;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HeaderSlotsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slot_type')
                    ->label('Mod')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        HeaderSlot::TYPE_BUTTON => 'Buton',
                        HeaderSlot::TYPE_BANNER => 'Banner',
                        default => $state,
                    }),

                ImageColumn::make('banner_image')
                    ->label('Banner')
                    ->disk('public'),

                TextColumn::make('display_position')
                    ->label('Konum')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        HeaderSlot::POSITION_TOPBAR_AFTER_HOME => 'Ana Sayfa yanı',
                        default => $state,
                    }),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('starts_at')
                    ->label('Başlangıç')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('ends_at')
                    ->label('Bitiş')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()->label('Görüntüle'),
                EditAction::make()->label('Düzenle'),
                DeleteAction::make()->label('Sil'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Seçilenleri Sil'),
                ]),
            ]);
    }
}
