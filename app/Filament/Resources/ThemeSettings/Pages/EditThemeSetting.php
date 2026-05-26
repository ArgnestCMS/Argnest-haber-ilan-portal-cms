<?php

namespace App\Filament\Resources\ThemeSettings\Pages;

use App\Filament\Resources\ThemeSettings\ThemeSettingResource;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditThemeSetting extends EditRecord
{
    protected static string $resource = ThemeSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            Action::make('reset_defaults')
                ->label('Varsayılana sıfırla')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Tema renklerini varsayılana sıfırla')
                ->modalDescription('Tüm tema renkleri mevcut tasarıma yakın varsayılan değerlere döndürülür.')
                ->action(function (): void {
                    $this->record->resetToDefaults();
                    $this->fillForm();
                }),
        ];
    }
}
