<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Helpers\ActivityLogger;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [

            ViewAction::make(),

            DeleteAction::make()
                ->after(function () {

                    ActivityLogger::log(
                        action: 'delete_user',

                        description: auth()->user()->name . ' kullanıcıyı sildi.',

                        properties: [
                            'deleted_user_id' => $this->record->id,
                            'name' => $this->record->name ?? null,
                            'email' => $this->record->email ?? null,
                            'role' => $this->record->role ?? null,
                        ]
                    );
                }),
        ];
    }

    protected function afterSave(): void
    {
        ActivityLogger::log(
            action: 'edit_user',

            description: auth()->user()->name . ' kullanıcıyı düzenledi.',

            properties: [
                'edited_user_id' => $this->record->id,
                'name' => $this->record->name ?? null,
                'email' => $this->record->email ?? null,
                'role' => $this->record->role ?? null,
                'is_active' => $this->record->is_active ?? null,
            ]
        );
    }
}