<?php

namespace App\Filament\Resources\WorkSessions\Pages;

use App\Filament\Resources\WorkSessions\WorkSessionResource;
use App\Helpers\ActivityLogger;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkSession extends CreateRecord
{
    protected static string $resource = WorkSessionResource::class;

    protected function afterCreate(): void
    {
        ActivityLogger::log(
            action: 'create_work_session',
            description: auth()->user()->name . ' yeni mesai kaydı oluşturdu.',
            properties: [
                'work_session_id' => $this->record->id,
                'status' => $this->record->status ?? null,
                'started_at' => $this->record->started_at ?? null,
                'ended_at' => $this->record->ended_at ?? null,
            ]
        );
    }
}