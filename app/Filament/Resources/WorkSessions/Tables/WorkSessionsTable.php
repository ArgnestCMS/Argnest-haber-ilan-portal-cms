<?php

namespace App\Filament\Resources\WorkSessions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WorkSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('started_at', 'desc')

            ->columns([

                TextColumn::make('user.name')
                    ->label('Çalışan')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('type')
                    ->label('Tür')
                    ->colors([
                        'success' => 'work',
                        'warning' => 'break',
                        'danger' => 'lunch',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'work' => 'Mesai',
                        'break' => 'Mola',
                        'lunch' => 'Yemek',
                        default => $state,
                    }),

                BadgeColumn::make('status')
                    ->label('Durum')
                    ->colors([
                        'success' => 'active',
                        'gray' => 'completed',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active' => 'Aktif',
                        'completed' => 'Tamamlandı',
                        default => $state,
                    }),

                TextColumn::make('started_at')
                    ->label('Başlangıç')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),

                TextColumn::make('ended_at')
                    ->label('Bitiş')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),

                TextColumn::make('duration_minutes')
                    ->label('Süre')
                    ->formatStateUsing(fn ($state) => $state . ' dk')
                    ->sortable(),

                TextColumn::make('ip_address')
                    ->label('IP Adresi')
                    ->searchable(),

                TextColumn::make('device')
                    ->label('Cihaz')
                    ->badge(),

                TextColumn::make('browser')
                    ->label('Tarayıcı')
                    ->badge(),

                TextColumn::make('platform')
                    ->label('Platform')
                    ->badge(),

            ])

            ->filters([

                SelectFilter::make('user_id')
                    ->label('Çalışan')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('type')
                    ->label('Tür')
                    ->options([
                        'work' => 'Mesai',
                        'break' => 'Mola',
                        'lunch' => 'Yemek',
                    ]),

                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'active' => 'Aktif',
                        'completed' => 'Tamamlandı',
                    ]),

                Filter::make('today')
                    ->label('Bugün')
                    ->query(fn (Builder $query): Builder => $query->whereDate('started_at', today())),

                Filter::make('this_week')
                    ->label('Bu Hafta')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('started_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek(),
                    ])),

                Filter::make('this_month')
                    ->label('Bu Ay')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereMonth('started_at', now()->month)
                        ->whereYear('started_at', now()->year)),

            ])

            ->recordActions([
                ViewAction::make()
                    ->label('Detay'),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Kayıtları Sil'),
                ]),
            ]);
    }
}