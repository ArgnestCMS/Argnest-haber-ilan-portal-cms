<?php

namespace App\Filament\Resources\WorkSessions\Pages;

use App\Filament\Resources\WorkSessions\WorkSessionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewWorkSession extends ViewRecord
{
    protected static string $resource = WorkSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
