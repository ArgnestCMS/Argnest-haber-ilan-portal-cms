<?php

namespace App\Filament\Resources\SeoSettings\Pages;

use App\Filament\Resources\SeoSettings\SeoSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSeoSetting extends EditRecord
{
    protected static string $resource = SeoSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
