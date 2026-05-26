<?php

namespace App\Filament\Resources\UserPunishments\Pages;

use App\Filament\Resources\UserPunishments\UserPunishmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserPunishments extends ListRecords
{
    protected static string $resource = UserPunishmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
