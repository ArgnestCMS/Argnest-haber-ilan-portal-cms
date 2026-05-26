<?php

namespace App\Filament\Resources\UserPunishments\Pages;

use App\Filament\Resources\UserPunishments\UserPunishmentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUserPunishment extends ViewRecord
{
    protected static string $resource = UserPunishmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
