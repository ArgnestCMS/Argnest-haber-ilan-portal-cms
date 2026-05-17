<?php

namespace App\Filament\Resources\UserPunishments\Pages;

use App\Filament\Resources\UserPunishments\UserPunishmentResource;
use App\Helpers\ActivityLogger;
use Filament\Resources\Pages\CreateRecord;

class CreateUserPunishment extends CreateRecord
{
    protected static string $resource = UserPunishmentResource::class;

    protected function afterCreate(): void
    {
        ActivityLogger::log(
            action: 'punishment_given',
            description: auth()->user()->name . ' kullanıcıya ceza verdi.',
            properties: [
                'punishment_id' => $this->record->id,
                'punished_user_id' => $this->record->user_id ?? null,
                'type' => $this->record->type ?? null,
                'reason' => $this->record->reason ?? null,
                'expires_at' => $this->record->expires_at ?? null,
                'is_active' => $this->record->is_active ?? null,
            ]
        );
    }
}