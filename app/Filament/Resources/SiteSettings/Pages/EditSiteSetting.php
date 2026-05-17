<?php

namespace App\Filament\Resources\SiteSettings\Pages;

use App\Filament\Resources\SiteSettings\SiteSettingResource;
use App\Helpers\ActivityLogger;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSiteSetting extends EditRecord
{
    protected static string $resource = SiteSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),

            DeleteAction::make()
                ->after(function () {
                    ActivityLogger::log(
                        action: 'delete_site_setting',
                        description: auth()->user()->name . ' site ayarını sildi.',
                        properties: [
                            'site_setting_id' => $this->record->id,
                            'site_name' => $this->record->site_name ?? null,
                        ]
                    );
                }),
        ];
    }

    protected function afterSave(): void
    {
        $maintenanceMode = (bool) ($this->record->maintenance_mode ?? false);

        ActivityLogger::log(
            action: 'edit_site_setting',
            description: auth()->user()->name . ' site ayarlarını güncelledi.',
            properties: [
                'site_setting_id' => $this->record->id,
                'site_name' => $this->record->site_name ?? null,
                'seo_title' => $this->record->seo_title ?? null,
                'maintenance_mode' => $maintenanceMode,
            ]
        );

        ActivityLogger::log(
            action: $maintenanceMode ? 'maintenance_mode_enabled' : 'maintenance_mode_disabled',
            description: $maintenanceMode
                ? auth()->user()->name . ' bakım modunu aktif etti.'
                : auth()->user()->name . ' bakım modunu pasif etti.',
            properties: [
                'site_setting_id' => $this->record->id,
                'maintenance_mode' => $maintenanceMode,
            ]
        );
    }
}