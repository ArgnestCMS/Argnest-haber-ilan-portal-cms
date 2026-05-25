<?php

namespace App\Filament\Resources\SiteSettings\Pages;

use App\Filament\Resources\SiteSettings\SiteSettingResource;
use App\Services\PortalCacheService;
use Filament\Resources\Pages\CreateRecord;

class CreateSiteSetting extends CreateRecord
{
    protected static string $resource = SiteSettingResource::class;

    protected function afterCreate(): void
    {
        app(PortalCacheService::class)->clearContent();
    }
}
