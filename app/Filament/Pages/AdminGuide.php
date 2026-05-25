<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class AdminGuide extends Page
{
    protected string $view = 'filament.pages.admin-guide';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'Kullanim Kilavuzu';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem Yonetimi';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'guide';

    protected static ?string $title = 'Kullanim Kilavuzu';

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('panel_giris');
    }

    public function getTitle(): string|Htmlable
    {
        return 'Kullanim Kilavuzu';
    }
}
