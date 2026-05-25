<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DatabaseBackupService
{
    private const DIRECTORY = 'backups/database';

    public function backups(): Collection
    {
        $this->ensureDirectoryExists();

        return collect(File::files($this->backupDirectory()))
            ->map(function (\SplFileInfo $file): array {
                return [
                    'name' => $file->getFilename(),
                    'path' => $file->getPathname(),
                    'created_at' => Carbon::createFromTimestamp($file->getMTime()),
                    'size' => $file->getSize(),
                    'status' => 'Hazir',
                ];
            })
            ->sortByDesc('created_at')
            ->values();
    }

    public function create(): array
    {
        $mysqldump = $this->findMysqlDump();

        if (! $mysqldump) {
            throw new RuntimeException('mysqldump bulunamadi. XAMPP kullaniyorsaniz C:\\xampp\\mysql\\bin\\mysqldump.exe yolunu girin.');
        }

        $this->ensureDirectoryExists();

        $database = $this->unquotedConfigValue((string) config('database.connections.mysql.database'));
        $username = $this->unquotedConfigValue((string) config('database.connections.mysql.username'));
        $password = (string) config('database.connections.mysql.password');

        if (blank($database) || blank($username)) {
            throw new RuntimeException('MySQL veritabani adi veya kullanici adi yapilandirilmamis.');
        }

        $fileName = 'database-backup-' . now()->format('Y-m-d-H-i-s') . '.sql';
        $absolutePath = $this->backupDirectory() . DIRECTORY_SEPARATOR . $fileName;

        $this->deletePartialBackup($absolutePath);

        $result = $this->runDump($mysqldump, $database, $username, $password, $absolutePath);

        clearstatcache(true, $absolutePath);

        if ($this->hasUsableBackupFile($absolutePath)) {
            return [
                'name' => $fileName,
                'path' => $absolutePath,
                'size' => File::size($absolutePath),
            ];
        }

        $this->deletePartialBackup($absolutePath);

        Log::warning('Database backup command failed.', [
            'mysqldump' => $mysqldump,
            'database' => $database,
            'exit_code' => $result['exit_code'],
            'error' => $result['error'],
        ]);

        throw new RuntimeException(
            $result['error'] ?: 'Veritabani yedegi alinamadi. MySQL ve mysqldump ayarlarini kontrol edin.'
        );
    }

    public function delete(string $fileName): void
    {
        $path = $this->pathForFileName($fileName);

        if (! File::exists($path)) {
            throw new RuntimeException('Yedek dosyasi bulunamadi.');
        }

        clearstatcache(true, $path);

        $deleteError = null;
        set_error_handler(function (int $severity, string $message) use (&$deleteError): bool {
            $deleteError = $message;

            return true;
        });

        try {
            $deleted = unlink($path);
        } finally {
            restore_error_handler();
        }

        if (! $deleted) {
            $error = $deleteError ?: 'unknown delete error';

            Log::warning('Database backup file could not be deleted.', [
                'path' => $path,
                'error' => $error,
            ]);

            throw new RuntimeException('Yedek dosyasi silinemedi. Dosya baska bir islem tarafindan kullaniliyor veya izin yok. Detay: ' . $error);
        }

        clearstatcache(true, $path);
    }

    public function deleteAll(): int
    {
        $this->ensureDirectoryExists();

        $deleted = 0;

        foreach (File::files($this->backupDirectory()) as $file) {
            try {
                $this->delete($file->getFilename());
                $deleted++;
            } catch (RuntimeException $exception) {
                throw new RuntimeException($file->getFilename() . ' silinemedi: ' . $exception->getMessage(), previous: $exception);
            }
        }

        return $deleted;
    }

    public function download(string $fileName): BinaryFileResponse
    {
        $path = $this->pathForFileName($fileName);

        if (! File::exists($path)) {
            throw new RuntimeException('Yedek dosyasi bulunamadi.');
        }

        return response()->download($path, $fileName);
    }

    public function humanSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = max($bytes, 0);
        $index = 0;

        while ($size >= 1024 && $index < count($units) - 1) {
            $size /= 1024;
            $index++;
        }

        return round($size, $index === 0 ? 0 : 2) . ' ' . $units[$index];
    }

    private function runDump(
        string $mysqldump,
        string $database,
        string $username,
        string $password,
        string $absolutePath,
    ): array {
        if (PHP_OS_FAMILY === 'Windows') {
            $arguments = [
                $mysqldump,
                '--no-defaults',
                '-u',
                $username,
            ];

            if ($password !== '') {
                $arguments[] = '--password=' . $password;
            }

            $arguments[] = $database;

            $command = $this->windowsCommandWithRedirect($arguments, $absolutePath);

            logger()->info('Database backup command started.', [
                'command' => $this->windowsCommandWithRedirect($this->redactedCommand($arguments, $password), $absolutePath),
            ]);

            return $this->runShellCommand($command, $password);
        }

        $arguments = [
            $mysqldump,
            '-u',
            $username,
        ];

        if ($password !== '') {
            $arguments[] = '--password=' . $password;
        }

        $arguments[] = $database;
        $command = $this->shellCommandWithRedirect($arguments, $absolutePath);

        logger()->info('Database backup command started.', [
            'command' => $this->shellCommandWithRedirect($this->redactedCommand($arguments, $password), $absolutePath),
        ]);

        return $this->runShellCommand($command, $password);
    }

    private function ensureDirectoryExists(): void
    {
        File::ensureDirectoryExists($this->backupDirectory());
    }

    private function pathForFileName(string $fileName): string
    {
        $baseName = basename($fileName);

        if ($baseName !== $fileName || $baseName === '' || $baseName === '.' || $baseName === '..') {
            throw new RuntimeException('Gecersiz yedek dosyasi.');
        }

        return $this->backupDirectory() . DIRECTORY_SEPARATOR . $baseName;
    }

    private function backupDirectory(): string
    {
        return storage_path('app/' . self::DIRECTORY);
    }

    private function deletePartialBackup(string $absolutePath): void
    {
        if (File::exists($absolutePath)) {
            File::delete($absolutePath);
        }
    }

    private function hasUsableBackupFile(string $absolutePath): bool
    {
        return File::exists($absolutePath) && File::size($absolutePath) > 0;
    }

    private function findMysqlDump(): ?string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $candidates = [
                'C:\\xampp\\mysql\\bin\\mysqldump.exe',
                config('backup.mysqldump_path'),
                ...(glob('C:\\laragon\\bin\\mysql\\mysql-*\\bin\\mysqldump.exe') ?: []),
                ...(glob('C:\\Program Files\\MySQL\\MySQL Server *\\bin\\mysqldump.exe') ?: []),
                'mysqldump',
            ];
        } else {
            $candidates = [
                config('backup.mysqldump_path'),
                'mysqldump',
                'C:\\xampp\\mysql\\bin\\mysqldump.exe',
                ...(glob('C:\\laragon\\bin\\mysql\\mysql-*\\bin\\mysqldump.exe') ?: []),
                ...(glob('C:\\Program Files\\MySQL\\MySQL Server *\\bin\\mysqldump.exe') ?: []),
            ];
        }

        foreach ($candidates as $candidate) {
            if (blank($candidate)) {
                continue;
            }

            $candidate = $this->unquotedConfigValue((string) $candidate);

            if ($candidate !== 'mysqldump' && ! File::exists($candidate)) {
                continue;
            }

            if ($candidate === 'mysqldump' || File::exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function unquotedConfigValue(string $value): string
    {
        return trim(trim($value), "\"'");
    }

    private function sanitizeProcessOutput(string $output, string $password): string
    {
        $message = str($output)
            ->squish()
            ->limit(500)
            ->toString();

        if ($password !== '') {
            $message = str_replace($password, '[redacted]', $message);
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $message = str_replace(['localhost', '127.0.0.1'], '[mysql-host]', $message);
        }

        return $message;
    }

    private function runShellCommand(string $command, string $password): array
    {
        $process = proc_open(
            $command,
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            base_path(),
        );

        if (! is_resource($process)) {
            return [
                'exit_code' => 1,
                'error' => 'mysqldump islemi baslatilamadi.',
            ];
        }

        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]) ?: '';
        $error = stream_get_contents($pipes[2]) ?: '';
        fclose($pipes[1]);
        fclose($pipes[2]);

        return [
            'exit_code' => proc_close($process),
            'error' => $this->sanitizeProcessOutput($error ?: $output, $password),
        ];
    }

    private function windowsCommandWithRedirect(array $arguments, string $absolutePath): string
    {
        return 'cmd /C "' . $this->shellCommandWithRedirect($arguments, $absolutePath) . '"';
    }

    private function shellCommandWithRedirect(array $arguments, string $absolutePath): string
    {
        return implode(' ', array_map('escapeshellarg', $arguments)) . ' > ' . escapeshellarg($absolutePath);
    }

    private function redactedCommand(array $command, string $password): array
    {
        if ($password === '') {
            return $command;
        }

        return array_map(
            fn (string $argument): string => $argument === '--password=' . $password ? '--password=[redacted]' : $argument,
            $command,
        );
    }
}
