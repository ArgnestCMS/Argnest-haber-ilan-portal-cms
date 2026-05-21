<?php

namespace App\Filament\Resources\HeaderSlots\Pages;

use App\Filament\Resources\HeaderSlots\HeaderSlotResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditHeaderSlot extends EditRecord
{
    protected static string $resource = HeaderSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->label('Görüntüle'),
        ];
    }
}
