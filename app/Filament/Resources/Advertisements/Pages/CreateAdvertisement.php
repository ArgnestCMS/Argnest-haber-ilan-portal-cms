<?php

namespace App\Filament\Resources\Advertisements\Pages;

use App\Filament\Resources\Advertisements\AdvertisementResource;
use App\Helpers\ActivityLogger;
use Filament\Resources\Pages\CreateRecord;

class CreateAdvertisement extends CreateRecord
{
    protected static string $resource = AdvertisementResource::class;

    protected function afterCreate(): void
    {
        ActivityLogger::log(
            action: 'create_advertisement',

            description: auth()->user()->name . ' yeni reklam oluşturdu.',

            properties: [
                'advertisement_id' => $this->record->id,
                'title' => $this->record->title ?? null,
                'position' => $this->record->position ?? null,
                'is_active' => $this->record->is_active ?? null,
            ]
        );
    }
}