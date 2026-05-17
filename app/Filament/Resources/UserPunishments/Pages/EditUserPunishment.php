<?php

namespace App\Filament\Resources\UserPunishments\Pages;

use App\Filament\Resources\UserPunishments\UserPunishmentResource;
use App\Helpers\ActivityLogger;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditUserPunishment extends EditRecord
{
    protected static string $resource = UserPunishmentResource::class;

    protected function getHeaderActions(): array
    {
        return [

            ViewAction::make(),

            DeleteAction::make()
                ->after(function () {

                    ActivityLogger::log(
                        action: 'delete_punishment',

                        description: auth()->user()->name . ' kullanıcı cezasını sildi.',

                        properties: [
                            'punishment_id' => $this->record->id,
                            'punished_user_id' => $this->record->user_id ?? null,
                            'type' => $this->record->type ?? null,
                            'reason' => $this->record->reason ?? null,
                        ]
                    );
                }),
        ];
    }

    protected function afterSave(): void
    {
        ActivityLogger::log(
            action: 'edit_punishment',

            description: auth()->user()->name . ' kullanıcı cezasını düzenledi.',

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