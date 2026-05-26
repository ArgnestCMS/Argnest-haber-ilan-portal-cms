<?php

namespace App\Filament\Resources\Advertisements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdvertisementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')

            ->columns([

                ImageColumn::make('image')
                    ->label('Görsel'),

                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('ad_type')
                    ->label('Tür')
                    ->colors([
                        'success' => 'image',
                        'warning' => 'html',
                        'danger' => 'adsense',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'image' => 'Görsel',
                        'html' => 'HTML',
                        'adsense' => 'Adsense',
                        default => $state,
                    }),

                TextColumn::make('position')
                    ->label('Konum')
                    ->badge(),

                TextColumn::make('device_target')
                    ->label('Cihaz')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'all' => 'Tümü',
                        'desktop' => 'Masaüstü',
                        'mobile' => 'Mobil',
                        default => $state,
                    }),

                TextColumn::make('page_target')
                    ->label('Sayfa')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'all' => 'Tümü',
                        'home' => 'Anasayfa',
                        'news' => 'Haber',
                        'announcement' => 'İlan',
                        'list' => 'Liste',
                        default => $state,
                    }),

                TextColumn::make('views')
                    ->label('Gösterim')
                    ->sortable(),

                TextColumn::make('clicks')
                    ->label('Tıklama')
                    ->sortable(),

                TextColumn::make('ctr')
                    ->label('CTR')
                    ->suffix('%')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])

            ->recordActions([

                EditAction::make()
                    ->label('Düzenle'),

                DeleteAction::make()
                    ->label('Sil'),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make()
                        ->label('Seçilenleri Sil'),

                ]),

            ]);
    }
}