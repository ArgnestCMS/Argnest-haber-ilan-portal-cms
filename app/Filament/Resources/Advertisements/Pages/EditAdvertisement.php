<?php

namespace App\Filament\Resources\Advertisements\Pages;

use App\Filament\Resources\Advertisements\AdvertisementResource;
use App\Helpers\ActivityLogger;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAdvertisement extends EditRecord
{
    protected static string $resource = AdvertisementResource::class;

    protected function getHeaderActions(): array
    {
        return [

            ViewAction::make(),

            DeleteAction::make()
                ->after(function () {

                    ActivityLogger::log(
                        action: 'delete_advertisement',

                        description: auth()->user()->name . ' reklamı sildi.',

                        properties: [
                            'advertisement_id' => $this->record->id,
                            'title' => $this->record->title ?? null,
                            'position' => $this->record->position ?? null,
                        ]
                    );
                }),
        ];
    }

    protected function afterSave(): void
    {
        ActivityLogger::log(
            action: 'edit_advertisement',

            description: auth()->user()->name . ' reklamı düzenledi.',

            properties: [
                'advertisement_id' => $this->record->id,
                'title' => $this->record->title ?? null,
                'position' => $this->record->position ?? null,
                'is_active' => $this->record->is_active ?? null,
            ]
        );
    }
}