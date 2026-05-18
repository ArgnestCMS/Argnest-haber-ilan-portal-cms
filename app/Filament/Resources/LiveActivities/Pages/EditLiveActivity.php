<?php

namespace App\Filament\Resources\LiveActivities\Pages;

use App\Filament\Resources\LiveActivities\LiveActivityResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLiveActivity extends EditRecord
{
    protected static string $resource = LiveActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
