<?php

namespace App\Filament\Widgets;

use App\Models\WorkSession;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class CurrentStaffStatus extends TableWidget
{
    protected static ?string $heading = 'Anlık Çalışan Durumu';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table

            ->query(
                WorkSession::query()
                    ->where('status', 'active')
                    ->latest('started_at')
            )

            ->defaultPaginationPageOption(10)

            ->columns([

                TextColumn::make('user.name')
                    ->label('Çalışan')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('type')
                    ->label('Anlık Durum')
                    ->colors([
                        'success' => 'work',
                        'warning' => 'break',
                        'danger' => 'lunch',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {

                        'work' => '🟢 Mesaide',
                        'break' => '☕ Molada',
                        'lunch' => '🍔 Yemekte',

                        default => $state,

                    }),

TextColumn::make('duration_minutes')
    ->label('Toplam Süre')
    ->formatStateUsing(function ($record) {

        if (! $record->started_at) {
            return '0 dk';
        }

        $minutes = $record->started_at->diffInMinutes(now());

        return intval($minutes) . ' dk';

    }),

                TextColumn::make('ip_address')
                    ->label('IP'),

                TextColumn::make('device')
                    ->label('Cihaz')
                    ->badge(),

                TextColumn::make('browser')
                    ->label('Tarayıcı')
                    ->badge(),

                TextColumn::make('platform')
                    ->label('Platform')
                    ->badge(),

            ]);
    }
 public static function canView(): bool
{
    return auth()->user()?->isAdmin()
        || auth()->user()?->hasPermission('kullanici_yonet');
}
}