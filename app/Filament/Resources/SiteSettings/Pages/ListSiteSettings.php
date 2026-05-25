<?php

namespace App\Filament\Resources\SiteSettings\Pages;

use App\Filament\Resources\SiteSettings\SiteSettingResource;
use App\Services\PortalCacheService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListSiteSettings extends ListRecords
{
    protected static string $resource = SiteSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clearPortalCache')
                ->label('Cache Temizle')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->action(function (): void {
                    $deleted = app(PortalCacheService::class)->clearAll();

                    Notification::make()
                        ->title('Portal cache temizlendi.')
                        ->body($deleted . ' cache anahtari silindi.')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
