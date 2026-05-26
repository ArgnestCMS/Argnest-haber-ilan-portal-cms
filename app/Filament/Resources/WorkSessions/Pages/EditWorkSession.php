<?php

namespace App\Filament\Resources\WorkSessions\Pages;

use App\Filament\Resources\WorkSessions\WorkSessionResource;
use App\Helpers\ActivityLogger;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditWorkSession extends EditRecord
{
    protected static string $resource = WorkSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [

            ViewAction::make(),

            DeleteAction::make()
                ->after(function () {

                    ActivityLogger::log(
                        action: 'delete_work_session',

                        description: auth()->user()->name . ' mesai kaydını sildi.',

                        properties: [
                            'work_session_id' => $this->record->id,
                            'status' => $this->record->status ?? null,
                            'started_at' => $this->record->started_at ?? null,
                            'ended_at' => $this->record->ended_at ?? null,
                        ]
                    );
                }),
        ];
    }

    protected function afterSave(): void
    {
        ActivityLogger::log(
            action: 'edit_work_session',

            description: auth()->user()->name . ' mesai kaydını düzenledi.',

            properties: [
                'work_session_id' => $this->record->id,
                'status' => $this->record->status ?? null,
                'started_at' => $this->record->started_at ?? null,
                'ended_at' => $this->record->ended_at ?? null,
            ]
        );
    }
}