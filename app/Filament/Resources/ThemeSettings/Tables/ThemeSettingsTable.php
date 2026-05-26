<?php

namespace App\Filament\Resources\ThemeSettings\Tables;

use App\Models\ThemeSetting;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ThemeSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('primary_color')->label('Ana'),
                ColorColumn::make('topbar_color')->label('Üst Bar'),
                ColorColumn::make('navbar_color')->label('Alt Menü'),
                ColorColumn::make('breaking_bar_color')->label('Son Dakika'),
                ColorColumn::make('button_color')->label('Buton'),
                ColorColumn::make('footer_color')->label('Footer'),
                TextColumn::make('updated_at')
                    ->label('Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('reset_defaults')
                    ->label('Varsayılana sıfırla')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Tema renklerini varsayılana sıfırla')
                    ->modalDescription('Tüm tema renkleri mevcut tasarıma yakın varsayılan değerlere döndürülür.')
                    ->action(fn (ThemeSetting $record) => $record->resetToDefaults()),
            ]);
    }
}
