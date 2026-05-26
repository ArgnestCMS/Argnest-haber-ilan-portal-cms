<?php

namespace App\Filament\Resources\HeaderSlots\Pages;

use App\Filament\Resources\HeaderSlots\HeaderSlotResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHeaderSlots extends ListRecords
{
    protected static string $resource = HeaderSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Header slot oluştur'),
        ];
    }
}
