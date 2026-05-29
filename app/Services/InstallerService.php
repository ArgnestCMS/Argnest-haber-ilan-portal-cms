<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Throwable;

class InstallerService
{
    private const LOCK_PATH = 'app/installed.lock';

    private array $lastContext = [];

    public function install(array $data): array
    {
        $this->prepareRuntime();
        $this->lastContext = ['stage' => 'start', 'database' => $data['DB_DATABASE'] ?? null];

        $this->ensureStorageDirectories();
        $this->assertEnvironmentWritable();
        $this->applyDatabaseConfig($data);
        $this->testConnection();

        $this->lastContext['stage'] = 'sql_import';
        $importedStatements = $this->importSql(database_path('install/backup.sql'));

        $this->lastContext['stage'] = 'cache_clear';
        $this->clearCaches();

        $this->lastContext['stage'] = 'installed_lock';
        $this->createInstallLock();

        $this->writeEnvironment($data);

        $this->log('Installation completed.', [
            'database' => $data['DB_DATABASE'],
            'imported_statements' => $importedStatements,
        ]);

        return [
            'database' => $data['DB_DATABASE'],
            'imported_statements' => $importedStatements,
        ];
    }

    public function friendlyError(Throwable $exception): string
    {
        $stage = $this->lastContext['stage'] ?? 'install';
        $message = $exception->getMessage();
        $lowerMessage = strtolower($message);

        if (str_contains($lowerMessage, 'access denied') || str_contains($lowerMessage, 'sqlstate[hy000] [1045]')) {
            return 'Veritabani kullanici adi veya sifresi hatali gorunuyor.';
        }

        if (str_contains($lowerMessage, 'unknown database') || str_contains($lowerMessage, 'sqlstate[hy000] [1049]')) {
            return 'Veritabani bulunamadi. Kurulumdan once hedef veritabanini olusturun.';
        }

        if (str_contains($lowerMessage, 'backup.sql')) {
            return 'database/install/backup.sql dosyasi okunamadi veya bulunamadi.';
        }

        return 'Kurulum "' . $stage . '" asamasinda durdu. Detay: ' . $message;
    }

    public function logFailure(Throwable $exception): void
    {
        $this->log('Installation failed.', [
            'stage' => $this->lastContext['stage'] ?? null,
            'database' => $this->lastContext['database'] ?? null,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    private function prepareRuntime(): void
    {
        @ignore_user_abort(true);
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '-1');
    }

    private function ensureStorageDirectories(): void
    {
        foreach ([
            storage_path('app'),
            storage_path('framework/sessions'),
            storage_path('framework/cache'),
            storage_path('framework/cache/data'),
            storage_path('framework/views'),
            storage_path('logs'),
        ] as $path) {
            File::ensureDirectoryExists($path, 0755, true);
        }
    }

    private function applyDatabaseConfig(array $data): void
    {
        $this->lastContext['stage'] = 'runtime_db_config';

        config([
            'database.connections.install' => [
                'driver' => 'mysql',
                'host' => $data['DB_HOST'],
                'port' => (string) $data['DB_PORT'],
                'database' => $data['DB_DATABASE'],
                'username' => $data['DB_USERNAME'],
                'password' => $data['DB_PASSWORD'] ?? '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => false,
                'engine' => null,
                'options' => extension_loaded('pdo_mysql') ? [
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                ] : [],
            ],
        ]);

        DB::purge('install');
    }

    private function testConnection(): void
    {
        $this->lastContext['stage'] = 'db_connection';

        DB::connection('install')->getPdo();
    }

    private function importSql(string $path): int
    {
        if (! File::exists($path) || ! File::isReadable($path)) {
            throw new RuntimeException('database/install/backup.sql dosyasi bulunamadi veya okunamadi.');
        }

        $pdo = DB::connection('install')->getPdo();
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
        $pdo->exec('SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO"');

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException('backup.sql dosyasi acilamadi.');
        }

        $statement = '';
        $count = 0;

        try {
            while (($line = fgets($handle)) !== false) {
                $trimmed = ltrim($line);

                if ($trimmed === '' || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '#')) {
                    continue;
                }

                if (preg_match('/^\/\*![0-9]{5}\s+(.*?)\s*\*\/;?\s*$/', trim($line), $matches) === 1) {
                    $line = $matches[1] . ';';
                }

                $statement .= $line;

                if (! $this->statementIsComplete($statement)) {
                    continue;
                }

                $sql = trim($statement);
                $statement = '';

                if ($sql === '') {
                    continue;
                }

                $pdo->exec($sql);
                $count++;
            }

            if (trim($statement) !== '') {
                $pdo->exec($statement);
                $count++;
            }
        } finally {
            fclose($handle);
            $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
        }

        return $count;
    }

    private function statementIsComplete(string $statement): bool
    {
        $inSingleQuote = false;
        $inDoubleQuote = false;
        $escaped = false;
        $length = strlen($statement);

        for ($i = 0; $i < $length; $i++) {
            $char = $statement[$i];

            if ($escaped) {
                $escaped = false;
                continue;
            }

            if ($char === '\\') {
                $escaped = true;
                continue;
            }

            if ($char === "'" && ! $inDoubleQuote) {
                $inSingleQuote = ! $inSingleQuote;
                continue;
            }

            if ($char === '"' && ! $inSingleQuote) {
                $inDoubleQuote = ! $inDoubleQuote;
            }
        }

        return ! $inSingleQuote && ! $inDoubleQuote && str_ends_with(rtrim($statement), ';');
    }

    private function writeEnvironment(array $data): void
    {
        $this->lastContext['stage'] = 'env_write';
        $path = base_path('.env');

        if (! File::exists($path)) {
            $example = base_path('.env.example');
            File::put($path, File::exists($example) ? File::get($example) : '');
        }

        if (! File::isWritable($path)) {
            throw new RuntimeException('.env dosyasi yazilabilir degil.');
        }

        $content = File::get($path);

        foreach ($data as $key => $value) {
            $content = $this->replaceEnvironmentValue($content, $key, (string) ($value ?? ''));
        }

        File::put($path, $content);
    }

    private function assertEnvironmentWritable(): void
    {
        $this->lastContext['stage'] = 'env_check';
        $path = base_path('.env');

        if (! File::exists($path)) {
            $example = base_path('.env.example');
            File::put($path, File::exists($example) ? File::get($example) : '');
        }

        if (! File::isWritable($path)) {
            throw new RuntimeException('.env dosyasi yazilabilir degil.');
        }
    }

    private function replaceEnvironmentValue(string $content, string $key, string $value): string
    {
        $line = $key . '=' . $this->quoteEnvironmentValue($value);

        if (preg_match('/^' . preg_quote($key, '/') . '=.*/m', $content) === 1) {
            return preg_replace('/^' . preg_quote($key, '/') . '=.*/m', $line, $content) ?? $content;
        }

        return rtrim($content) . PHP_EOL . $line . PHP_EOL;
    }

    private function quoteEnvironmentValue(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (preg_match('/\s|#|"|\'/', $value) === 1) {
            return '"' . str_replace(['\\', '"'], ['\\\\', '\\"'], $value) . '"';
        }

        return $value;
    }

    private function createInstallLock(): void
    {
        File::put(storage_path(self::LOCK_PATH), now()->toISOString() . PHP_EOL);

        if (! File::exists(storage_path(self::LOCK_PATH))) {
            throw new RuntimeException('installed.lock dosyasi olusturulamadi.');
        }
    }

    private function clearCaches(): void
    {
        foreach (['optimize:clear', 'config:clear'] as $command) {
            $exitCode = Artisan::call($command, ['--no-interaction' => true]);

            if ($exitCode !== 0) {
                $this->log($command . ' command failed after install.', [
                    'output' => trim(Artisan::output()),
                ]);
            }
        }
    }

    private function log(string $message, array $context = []): void
    {
        $this->ensureStorageDirectories();

        $line = '[' . now()->toDateTimeString() . '] ' . $message . ' ' . json_encode($context, JSON_UNESCAPED_SLASHES) . PHP_EOL;

        File::append(storage_path('logs/install.log'), $line);
    }
}
