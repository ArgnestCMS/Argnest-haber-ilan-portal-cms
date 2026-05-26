<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Helpers\ActivityLogger;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        ActivityLogger::log(
            action: 'create_user',
            description: auth()->user()->name . ' yeni kullanıcı oluşturdu.',
            properties: [
                'created_user_id' => $this->record->id,
                'name' => $this->record->name ?? null,
                'email' => $this->record->email ?? null,
                'role' => $this->record->role ?? null,
                'is_active' => $this->record->is_active ?? null,
            ]
        );
    }
}