<?php

namespace App\Filament\Resources\SeoSettings\Pages;

use App\Filament\Resources\SeoSettings\SeoSettingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSeoSetting extends ViewRecord
{
    protected static string $resource = SeoSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
