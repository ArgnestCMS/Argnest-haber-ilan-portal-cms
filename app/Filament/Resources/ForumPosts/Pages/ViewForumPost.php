<?php

namespace App\Filament\Resources\ForumPosts\Pages;

use App\Filament\Resources\ForumPosts\ForumPostResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewForumPost extends ViewRecord
{
    protected static string $resource = ForumPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
