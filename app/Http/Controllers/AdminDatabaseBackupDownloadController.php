<?php

namespace App\Http\Controllers;

use App\Services\DatabaseBackupService;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminDatabaseBackupDownloadController extends Controller
{
    public function __invoke(string $filename, DatabaseBackupService $backups): BinaryFileResponse|RedirectResponse
    {
        $user = auth()->user();

        if (
            ! $user
            || (
                ! $user->isAdmin()
                && $user->role !== 'super_admin'
                && $user->roleModel?->slug !== 'super_admin'
            )
        ) {
            abort(403);
        }

        try {
            return $backups->download($filename);
        } catch (RuntimeException $exception) {
            Notification::make()
                ->title('Yedek dosyası bulunamadı.')
                ->body($exception->getMessage())
                ->danger()
                ->send();

            return redirect()->to('/admin/database-backups');
        }
    }
}
