<?php

namespace App\Filament\Resources\HeaderSlots\Pages;

use App\Filament\Resources\HeaderSlots\HeaderSlotResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewHeaderSlot extends ViewRecord
{
    protected static string $resource = HeaderSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Düzenle'),
        ];
    }
}
