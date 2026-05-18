<?php

namespace App\Filament\Resources\ForumTags\Pages;

use App\Filament\Resources\ForumTags\ForumTagResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewForumTag extends ViewRecord
{
    protected static string $resource = ForumTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
