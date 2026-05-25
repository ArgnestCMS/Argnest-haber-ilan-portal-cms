<?php

namespace App\Filament\Pages;

use App\Services\DatabaseBackupService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DatabaseBackups extends Page
{
    protected string $view = 'filament.pages.database-backups';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    protected static ?string $navigationLabel = 'Veritabanı Yedekleri';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem Yönetimi';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'database-backups';

    protected static ?string $title = 'Veritabanı Yedekleri';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user?->isAdmin()
            || $user?->role === 'super_admin'
            || $user?->roleModel?->slug === 'super_admin';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Veritabanı Yedekleri';
    }

    public function backups(): array
    {
        return app(DatabaseBackupService::class)->backups()->all();
    }

    public function createBackup(): void
    {
        try {
            $backup = app(DatabaseBackupService::class)->create();

            Notification::make()
                ->title('Veritabanı yedeği alındı.')
                ->body($backup['name'] . ' oluşturuldu.')
                ->success()
                ->send();
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('Yedek alınamadı.')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteBackup(string $fileName): void
    {
        try {
            app(DatabaseBackupService::class)->delete($fileName);

            Notification::make()
                ->title('Yedek silindi.')
                ->success()
                ->send();
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('Yedek silinemedi.')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteAllBackups(): void
    {
        try {
            $count = app(DatabaseBackupService::class)->deleteAll();

            Notification::make()
                ->title('Yedekler temizlendi.')
                ->body($count . ' dosya silindi.')
                ->success()
                ->send();
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('Yedekler temizlenemedi.')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    public function downloadBackup(string $fileName): StreamedResponse
    {
        return app(DatabaseBackupService::class)->download($fileName);
    }

    public function humanSize(int $bytes): string
    {
        return app(DatabaseBackupService::class)->humanSize($bytes);
    }
}
