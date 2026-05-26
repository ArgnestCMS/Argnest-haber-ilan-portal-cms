<?php

namespace App\Filament\Resources\ThemeSettings\Pages;

use App\Filament\Resources\ThemeSettings\ThemeSettingResource;
use App\Models\ThemeSetting;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListThemeSettings extends ListRecords
{
    protected static string $resource = ThemeSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tema ayarı oluştur')
                ->visible(fn (): bool => ! ThemeSetting::query()->exists()),
        ];
    }
}
