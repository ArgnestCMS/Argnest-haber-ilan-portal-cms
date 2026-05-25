<?php

namespace App\Filament\Resources\SeoSettings\Pages;

use App\Filament\Resources\SeoSettings\SeoSettingResource;
use App\Services\SitemapService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSeoSetting extends EditRecord
{
    protected static string $resource = SeoSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clearSitemapCache')
                ->label('Sitemap cache temizle')
                ->icon('heroicon-o-arrow-path')
                ->action(function (): void {
                    app(SitemapService::class)->clearCache();

                    Notification::make()
                        ->title('Sitemap cache temizlendi.')
                        ->success()
                        ->send();
                }),
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
