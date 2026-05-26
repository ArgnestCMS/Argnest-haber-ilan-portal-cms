<?php

namespace App\Filament\Resources\LiveActivities\Pages;

use App\Filament\Resources\LiveActivities\LiveActivityResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLiveActivity extends ViewRecord
{
    protected static string $resource = LiveActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
