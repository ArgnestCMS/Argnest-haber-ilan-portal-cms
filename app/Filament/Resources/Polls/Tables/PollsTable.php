<?php

namespace App\Filament\Resources\Polls\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PollsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Anket')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('topic')
                    ->label('Konu')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('options_sum_votes_count')
                    ->label('Oy')
                    ->state(fn ($record) => $record->options()->sum('votes_count')),
                TextColumn::make('participants_count')
                    ->label('Katılım')
                    ->state(fn ($record) => $record->votes()->distinct('voter_key')->count('voter_key')),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                IconColumn::make('show_home_popup')
                    ->label('Popup')
                    ->boolean(),
                IconColumn::make('share_results')
                    ->label('Sonuç')
                    ->boolean(),
                TextColumn::make('starts_at')
                    ->label('Başlangıç')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('-'),
                TextColumn::make('ends_at')
                    ->label('Bitiş')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('-'),
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
