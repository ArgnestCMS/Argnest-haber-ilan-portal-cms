<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class OnlineUsersWidget extends TableWidget
{
    protected static ?string $heading = 'Online Kullanıcılar';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->whereNotNull('last_seen_at')
                    ->where('last_seen_at', '>=', now()->subMinutes(5))
                    ->latest('last_seen_at')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Kullanıcı')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'admin' => 'Admin',
                        'moderator' => 'Moderatör',
                        'editor' => 'Editör',
                        'user' => 'Kullanıcı',
                        default => $state ?? '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'admin' => 'danger',
                        'moderator' => 'warning',
                        'editor' => 'info',
                        'user' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('last_seen_at')
                    ->label('Son Aktif')
                    ->since()
                    ->sortable(),

                TextColumn::make('last_ip_address')
                    ->label('IP')
                    ->copyable()
                    ->placeholder('-'),

                TextColumn::make('last_browser')
                    ->label('Tarayıcı')
                    ->badge()
                    ->placeholder('-'),

                TextColumn::make('last_platform')
                    ->label('Platform')
                    ->badge()
                    ->placeholder('-'),
            ]);
    }

    public static function canView(): bool
{
    return auth()->user()?->isAdmin()
        || auth()->user()?->hasPermission('kullanici_yonet');
}
}