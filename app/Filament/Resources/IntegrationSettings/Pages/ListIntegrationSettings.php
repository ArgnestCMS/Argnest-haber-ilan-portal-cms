<?php

namespace App\Filament\Resources\IntegrationSettings\Pages;

use App\Filament\Resources\IntegrationSettings\IntegrationSettingResource;
use App\Models\IntegrationSetting;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListIntegrationSettings extends ListRecords
{
    protected static string $resource = IntegrationSettingResource::class;

    public function mount(): void
    {
        IntegrationSetting::current();

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit_current')
                ->label('Sistem ayarlarını düzenle')
                ->icon('heroicon-o-pencil-square')
                ->url(fn (): string => IntegrationSettingResource::getUrl('edit', [
                    'record' => IntegrationSetting::current(),
                ])),
        ];
    }
}
