<?php

namespace App\Filament\Resources\ForumTags\Pages;

use App\Filament\Resources\ForumTags\ForumTagResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditForumTag extends EditRecord
{
    protected static string $resource = ForumTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
