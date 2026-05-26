<?php

namespace App\Http\Controllers;

use App\Models\IntegrationSetting;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SeoSetting;
use App\Models\SiteSetting;
use App\Models\ThemeSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;
use PDO;
use PDOException;
use RuntimeException;
use Throwable;
use Illuminate\View\View;

class InstallController extends Controller
{
    private const LOCK_PATH = 'app/installed.lock';

    private const STEPS = [
        1 => 'Hos Geldiniz',
        2 => 'Sistem Gereksinimleri',
        3 => 'Veritabani Ayarlari',
        4 => 'Site Ayarlari',
        5 => 'Admin Kullanicisi',
        6 => 'Mail Ayarlari',
        7 => 'Kurulumu Tamamla',
    ];

    public function show(Request $request): View|RedirectResponse
    {
        if ($this->isInstalled()) {
            return redirect()->route('login');
        }

        $step = $this->normalizeStep((int) $request->query('step', 1));

        return view('install.wizard', [
            'step' => $step,
            'steps' => self::STEPS,
            'data' => $request->session()->get('install.data', $this->defaultData()),
            'requirements' => $this->requirements(),
            'version' => config('portal.version', 'v1.0.0') . ' - ' . config('portal.codename', 'Genesis'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($this->isInstalled()) {
            return redirect()->route('login');
        }

        $step = $this->normalizeStep((int) $request->input('step', 1));
        $action = (string) $request->input('action', 'next');

        if ($action === 'test-db') {
            $validated = $request->validate($this->rulesForStep(3));
            $this->mergeInstallData($request, $validated);

            return $this->testDatabaseConnection($validated)
                ? back()->with('status', 'Veritabani baglantisi basarili.')
                : back()->withErrors(['db_connection' => 'Veritabani baglantisi kurulamadi. Bilgileri kontrol edin.']);
        }

        $validated = $request->validate($this->rulesForStep($step));
        $this->mergeInstallData($request, $validated);

        if ($action === 'complete') {
            return $this->complete($request);
        }

        return redirect()->route('install', ['step' => min($step + 1, 7)]);
    }

    private function complete(Request $request): RedirectResponse
    {
        $this->extendInstallRuntime();

        $data = array_replace($this->defaultData(), $request->session()->get('install.data', []));
        $stage = 'baslatiliyor';

        validator($data, $this->completionRules())->validate();

        if (! $this->testDatabaseConnection($data)) {
            return redirect()
                ->route('install', ['step' => 3])
                ->withErrors(['db_connection' => 'Kurulum oncesi veritabani baglantisi dogrulanamadi.']);
        }

        try {
            $this->logInstallStage($stage, 'basladi', $data);
            $this->writeEnvironment($data);
            $this->applyRuntimeConfig($data);

            DB::purge('mysql');
            DB::reconnect('mysql');

            $existingTables = $this->databaseTables();

            if ($existingTables !== []) {
                if ($this->databaseResetRequested($request) && ! $this->databaseResetConfirmed($request)) {
                    return redirect()
                        ->route('install', ['step' => 7])
                        ->withInput()
                        ->withErrors([
                            'database' => 'Veritabanini sifirlamak icin checkbox isaretlenmeli ve onay alanina tam olarak RESET yazilmalidir.',
                        ]);
                }

                if (! $this->databaseResetConfirmed($request)) {
                    return redirect()
                        ->route('install', ['step' => 7])
                        ->withInput()
                        ->withErrors([
                            'database' => 'Bu veritabaninda tablolar var. Temiz kurulum icin bos bir veritabani secin veya "Veritabanini sifirla ve devam et" secenegini acik onayla isaretleyin. Bulunan tablolar: ' . implode(', ', array_slice($existingTables, 0, 8)) . (count($existingTables) > 8 ? '...' : ''),
                        ]);
                }

                $stage = 'db_reset';
                $this->logInstallStage($stage, 'basladi', $data, ['tables' => $existingTables]);
                $this->resetDatabase($existingTables);
                $this->logInstallStage($stage, 'bitti', $data);
            }

            $stage = 'migrate';
            $this->logInstallStage($stage, 'basladi', $data);
            $this->runMigrations();
            $this->logInstallStage($stage, 'bitti', $data);

            $admin = DB::transaction(function () use ($data, &$stage): User {
                $stage = 'roles_permissions';
                $this->logInstallStage($stage, 'basladi', $data);
                $adminRole = $this->createDefaultRolesAndPermissions();
                $this->logInstallStage($stage, 'bitti', $data);

                $stage = 'default_settings';
                $this->logInstallStage($stage, 'basladi', $data);
                $this->createDefaultSettings($data);
                $this->assertDefaultSettingsCreated();
                $this->logInstallStage($stage, 'bitti', $data);

                $stage = 'admin_user';
                $this->logInstallStage($stage, 'basladi', $data);
                $admin = $this->createAdminUser($data, $adminRole);
                $this->assertAdminUserCreated($admin);
                $this->logInstallStage($stage, 'bitti', $data);

                return $admin;
            });

            $stage = 'installed_lock';
            $this->logInstallStage($stage, 'basladi', $data);
            $this->createInstallLock();
            $this->logInstallStage($stage, 'bitti', $data);

            $request->session()->forget('install.data');
            Auth::login($admin);
            $request->session()->regenerate();

            $stage = 'redirect';
            $this->logInstallStage($stage, 'basladi', $data);

            return redirect('/admin/welcome');
        } catch (Throwable $exception) {
            Log::error('Install failed.', [
                'stage' => $stage,
                'database' => $data['db_database'] ?? null,
                'error' => $exception->getMessage(),
                'exception' => $exception,
            ]);

            return redirect()
                ->route('install', ['step' => 7])
                ->withInput()
                ->withErrors([
                    'install' => $this->friendlyInstallError($exception, $stage),
                ]);
        }
    }

    private function applyRuntimeConfig(array $data): void
    {
        config([
            'database.connections.mysql.host' => $data['db_host'],
            'database.connections.mysql.port' => $data['db_port'],
            'database.connections.mysql.database' => $data['db_database'],
            'database.connections.mysql.username' => $data['db_username'],
            'database.connections.mysql.password' => $data['db_password'] ?? '',
            'app.name' => $data['site_name'],
            'app.url' => $data['site_url'],
            'app.locale' => $data['default_language'],
            'app.timezone' => $data['default_timezone'],
            'session.driver' => config('session.driver') ?: 'file',
            'cache.default' => config('cache.default') ?: 'file',
        ]);
    }

    private function extendInstallRuntime(): void
    {
        @ignore_user_abort(true);
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
    }

    private function runMigrations(): void
    {
        $command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(base_path('artisan')) . ' migrate --force --no-interaction';
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
            throw new RuntimeException('Migration islemi baslatilamadi.');
        }

        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]) ?: '';
        $error = stream_get_contents($pipes[2]) ?: '';
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            throw new RuntimeException($this->summarizeProcessOutput($error ?: $output) ?: 'Migration islemi basarisiz oldu.');
        }
    }

    private function summarizeProcessOutput(string $output): string
    {
        return str($output)
            ->squish()
            ->limit(700)
            ->toString();
    }

    private function databaseTables(): array
    {
        $rows = DB::select("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");

        return collect($rows)
            ->map(function (object $row): ?string {
                $values = array_values((array) $row);

                return isset($values[0]) ? (string) $values[0] : null;
            })
            ->filter()
            ->values()
            ->all();
    }

    private function databaseResetConfirmed(Request $request): bool
    {
        return $request->boolean('reset_database')
            && strtoupper(trim((string) $request->input('reset_database_confirmation'))) === 'RESET';
    }

    private function databaseResetRequested(Request $request): bool
    {
        return $request->boolean('reset_database')
            || trim((string) $request->input('reset_database_confirmation')) !== '';
    }

    private function resetDatabase(array $tables): void
    {
        if ($tables === []) {
            return;
        }

        $quotedTables = array_map(
            fn (string $table): string => '`' . str_replace('`', '``', $table) . '`',
            $tables,
        );

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            DB::statement('DROP TABLE IF EXISTS ' . implode(', ', $quotedTables));
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    private function friendlyInstallError(Throwable $exception, string $stage): string
    {
        $message = strtolower($exception->getMessage());
        $stageLabel = $this->installStageLabel($stage);

        if (str_contains($message, 'base table or view already exists')
            || str_contains($message, 'already exists')
            || str_contains($message, 'sqlstate[42s01]')
        ) {
            return $stageLabel . ' asamasinda kurulum durdu: Bu veritabaninda daha once olusturulmus tablolar var. Bos bir veritabani secin veya son adimda "Veritabanini sifirla ve devam et" onayini kullanin.';
        }

        return $stageLabel . ' asamasinda kurulum durdu. Veritabani ve dosya izinlerini kontrol edip tekrar deneyin. Detay: ' . $exception->getMessage();
    }

    private function installStageLabel(string $stage): string
    {
        return match ($stage) {
            'db_reset' => 'Veritabani sifirlama',
            'migrate' => 'Migration',
            'default_settings' => 'Varsayilan ayarlar',
            'roles_permissions' => 'Rol ve yetkiler',
            'admin_user' => 'Admin kullanicisi',
            'installed_lock' => 'Kurulum kilidi',
            'redirect' => 'Yonlendirme',
            default => 'Kurulum',
        };
    }

    private function logInstallStage(string $stage, string $status, array $data, array $context = []): void
    {
        Log::info('Install stage ' . $status . ': ' . $stage, array_merge([
            'stage' => $stage,
            'database' => $data['db_database'] ?? null,
        ], $context));
    }

    private function rulesForStep(int $step): array
    {
        return match ($step) {
            3 => [
                'db_host' => ['required', 'string', 'max:255'],
                'db_port' => ['required', 'integer', 'min:1', 'max:65535'],
                'db_database' => ['required', 'string', 'max:255'],
                'db_username' => ['required', 'string', 'max:255'],
                'db_password' => ['nullable', 'string', 'max:255'],
            ],
            4 => [
                'site_name' => ['required', 'string', 'max:120'],
                'site_description' => ['nullable', 'string', 'max:500'],
                'site_url' => ['required', 'url', 'max:255'],
                'default_language' => ['required', 'string', 'max:10'],
                'default_timezone' => ['required', 'timezone'],
            ],
            5 => [
                'admin_name' => ['required', 'string', 'max:120'],
                'admin_username' => ['required', 'string', 'alpha_dash', 'max:80'],
                'admin_email' => ['required', 'email', 'max:255'],
                'admin_password' => ['required', 'confirmed', Password::min(10)->mixedCase()->numbers()],
                'admin_password_confirmation' => ['required', 'string'],
            ],
            6 => [
                'mail_host' => ['nullable', 'string', 'max:255'],
                'mail_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
                'mail_username' => ['nullable', 'string', 'max:255'],
                'mail_password' => ['nullable', 'string', 'max:255'],
                'mail_encryption' => ['nullable', 'in:,tls,ssl'],
                'mail_from_address' => ['nullable', 'email', 'max:255'],
            ],
            default => [],
        };
    }

    private function completionRules(): array
    {
        return array_merge(
            $this->rulesForStep(3),
            $this->rulesForStep(4),
            $this->rulesForStep(5),
            $this->rulesForStep(6),
        );
    }

    private function mergeInstallData(Request $request, array $data): void
    {
        $current = $request->session()->get('install.data', $this->defaultData());
        $request->session()->put('install.data', array_replace($current, $data));
    }

    private function defaultData(): array
    {
        return [
            'db_host' => env('DB_HOST', '127.0.0.1'),
            'db_port' => env('DB_PORT', '3306'),
            'db_database' => env('DB_DATABASE', ''),
            'db_username' => env('DB_USERNAME', ''),
            'db_password' => '',
            'site_name' => config('portal.name', 'Argnest Haber-İlan Portal CMS'),
            'site_description' => config('portal.tagline', 'Modern Haber, İlan ve Topluluk Yönetim Sistemi'),
            'site_url' => config('app.url', 'http://localhost'),
            'default_language' => 'tr',
            'default_timezone' => config('app.timezone', 'Europe/Istanbul'),
            'admin_name' => '',
            'admin_username' => '',
            'admin_email' => '',
            'admin_password' => '',
            'admin_password_confirmation' => '',
            'mail_host' => env('MAIL_HOST', ''),
            'mail_port' => env('MAIL_PORT', '587'),
            'mail_username' => env('MAIL_USERNAME', ''),
            'mail_password' => '',
            'mail_encryption' => env('MAIL_SCHEME', env('MAIL_ENCRYPTION', 'tls')),
            'mail_from_address' => env('MAIL_FROM_ADDRESS', ''),
        ];
    }

    private function requirements(): array
    {
        $extensions = ['pdo', 'pdo_mysql', 'openssl', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'fileinfo'];

        $checks = [
            ['label' => 'PHP 8.2+', 'ok' => version_compare(PHP_VERSION, '8.2.0', '>=')],
            ['label' => 'storage yazilabilir', 'ok' => File::isWritable(storage_path())],
            ['label' => 'bootstrap/cache yazilabilir', 'ok' => File::isWritable(base_path('bootstrap/cache'))],
            ['label' => 'GD veya ImageMagick', 'ok' => extension_loaded('gd') || extension_loaded('imagick')],
            ['label' => 'Mevcut MySQL baglantisi', 'ok' => $this->currentDatabaseConnectionWorks()],
        ];

        foreach ($extensions as $extension) {
            $checks[] = ['label' => 'PHP extension: ' . $extension, 'ok' => extension_loaded($extension)];
        }

        return $checks;
    }

    private function currentDatabaseConnectionWorks(): bool
    {
        try {
            DB::select('select 1');

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    private function testDatabaseConnection(array $data): bool
    {
        try {
            new PDO(
                sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $data['db_host'], $data['db_port'], $data['db_database']),
                $data['db_username'],
                $data['db_password'] ?? '',
                [PDO::ATTR_TIMEOUT => 5, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
            );

            return true;
        } catch (PDOException) {
            return false;
        }
    }

    private function createDefaultRolesAndPermissions(): Role
    {
        $permissions = [
            ['name' => 'Panel Girisi', 'slug' => 'panel_giris', 'group' => 'Panel'],
            ['name' => 'Haber Goruntuleme', 'slug' => 'haber_gor', 'group' => 'Haber'],
            ['name' => 'Haber Ekleme', 'slug' => 'haber_ekle', 'group' => 'Haber'],
            ['name' => 'Haber Duzenleme', 'slug' => 'haber_duzenle', 'group' => 'Haber'],
            ['name' => 'Haber Silme', 'slug' => 'haber_sil', 'group' => 'Haber'],
            ['name' => 'Ilan Goruntuleme', 'slug' => 'ilan_gor', 'group' => 'Ilan'],
            ['name' => 'Ilan Ekleme', 'slug' => 'ilan_ekle', 'group' => 'Ilan'],
            ['name' => 'Ilan Duzenleme', 'slug' => 'ilan_duzenle', 'group' => 'Ilan'],
            ['name' => 'Ilan Silme', 'slug' => 'ilan_sil', 'group' => 'Ilan'],
            ['name' => 'SEO Yonetimi', 'slug' => 'seo_yonet', 'group' => 'SEO'],
            ['name' => 'Site Ayarlari', 'slug' => 'site_ayarlarini_yonet', 'group' => 'Site'],
            ['name' => 'Kullanici Yonetimi', 'slug' => 'kullanici_yonet', 'group' => 'Kullanici'],
            ['name' => 'Forum Yonetimi', 'slug' => 'forum_yonet', 'group' => 'Forum'],
            ['name' => 'Forum Moderasyonu', 'slug' => 'forum_moderasyonu', 'group' => 'Forum'],
            ['name' => 'Yorum Moderasyonu', 'slug' => 'yorum_moderasyonu', 'group' => 'Moderasyon'],
            ['name' => 'Reklam Yonetimi', 'slug' => 'reklam_yonet', 'group' => 'Reklam'],
            ['name' => 'Video Ekleme', 'slug' => 'video_ekle', 'group' => 'Medya'],
            ['name' => 'Video Duzenleme', 'slug' => 'video_duzenle', 'group' => 'Medya'],
            ['name' => 'Galeri Ekleme', 'slug' => 'galeri_ekle', 'group' => 'Medya'],
            ['name' => 'Galeri Duzenleme', 'slug' => 'galeri_duzenle', 'group' => 'Medya'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(['slug' => $permission['slug']], $permission);
        }

        $admin = Role::query()->updateOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin', 'color' => 'danger', 'is_system' => true, 'description' => 'Tam yetkili sistem yoneticisi.'],
        );

        $editor = Role::query()->updateOrCreate(['slug' => 'editor'], ['name' => 'Editor', 'color' => 'warning', 'is_system' => true]);
        $moderator = Role::query()->updateOrCreate(['slug' => 'moderator'], ['name' => 'Moderator', 'color' => 'success', 'is_system' => true]);
        Role::query()->updateOrCreate(['slug' => 'user'], ['name' => 'Kullanici', 'color' => 'gray', 'is_system' => true]);

        $admin->permissions()->sync(Permission::query()->pluck('id'));
        $editor->permissions()->sync(Permission::query()
            ->whereIn('slug', [
                'panel_giris',
                'haber_gor',
                'haber_ekle',
                'haber_duzenle',
                'ilan_gor',
                'ilan_ekle',
                'ilan_duzenle',
                'video_ekle',
                'video_duzenle',
                'galeri_ekle',
                'galeri_duzenle',
            ])
            ->pluck('id'));
        $moderator->permissions()->sync(Permission::query()
            ->whereIn('slug', ['panel_giris', 'yorum_moderasyonu', 'forum_moderasyonu'])
            ->pluck('id'));

        return $admin;
    }

    private function createDefaultSettings(array $data): void
    {
        SiteSetting::query()->firstOrCreate([])->forceFill([
            'site_name' => $data['site_name'],
            'site_slogan' => $data['site_description'],
            'forum_enabled' => true,
            'registration_enabled' => true,
            'email_verification_required' => false,
            'maintenance_mode' => false,
            'maintenance_message' => null,
            'maintenance_ends_at' => null,
            'home_news_enabled' => true,
            'home_announcements_enabled' => true,
            'home_forum_enabled' => true,
            'home_galleries_enabled' => true,
            'home_videos_enabled' => true,
            'home_polls_enabled' => false,
            'home_breaking_news_enabled' => false,
            'home_announcement_bar_enabled' => false,
            'seo_title' => $data['site_name'],
            'seo_description' => $data['site_description'],
            'footer_copyright' => now()->year . ' ' . $data['site_name'],
        ])->save();

        SeoSetting::query()->firstOrCreate([])->forceFill([
            'site_title' => $data['site_name'],
            'site_description' => $data['site_description'],
            'default_language' => $data['default_language'],
            'canonical_url' => $data['site_url'],
            'indexing' => true,
            'robots_index' => true,
            'robots_follow' => true,
            'robots_txt' => "User-agent: *\nAllow: /\nSitemap: " . rtrim($data['site_url'], '/') . '/sitemap.xml',
            'sitemap_cache_minutes' => 60,
        ])->save();

        ThemeSetting::query()->firstOrCreate([])->forceFill(ThemeSetting::DEFAULTS)->save();

        IntegrationSetting::query()->firstOrCreate([])->forceFill([
            'mail_mailer' => 'smtp',
            'mail_host' => $data['mail_host'] ?: null,
            'mail_port' => $data['mail_port'] ?: null,
            'mail_username' => $data['mail_username'] ?: null,
            'mail_password' => $data['mail_password'] ?: null,
            'mail_encryption' => $data['mail_encryption'] ?: null,
            'mail_from_address' => $data['mail_from_address'] ?: null,
            'mail_from_name' => $data['site_name'],
            'recaptcha_enabled' => false,
            'captcha_required' => false,
        ])->save();
    }

    private function assertDefaultSettingsCreated(): void
    {
        $models = [
            SiteSetting::class => 'SiteSetting',
            SeoSetting::class => 'SeoSetting',
            ThemeSetting::class => 'ThemeSetting',
            IntegrationSetting::class => 'IntegrationSetting',
        ];

        foreach ($models as $model => $label) {
            if (! $model::query()->exists()) {
                throw new RuntimeException($label . ' default kaydi olusturulamadi.');
            }
        }
    }

    private function createAdminUser(array $data, Role $adminRole): User
    {
        $attributes = [
            'name' => $data['admin_name'],
            'email' => $data['admin_email'],
            'password' => Hash::make($data['admin_password']),
            'role' => 'admin',
            'role_id' => $adminRole->id,
            'status' => 'active',
            'is_active' => true,
            'email_verified_at' => now(),
        ];

        if (Schema::hasColumn('users', 'username')) {
            $attributes['username'] = $data['admin_username'];
        }

        return User::query()->updateOrCreate(['email' => $data['admin_email']], $attributes);
    }

    private function assertAdminUserCreated(User $admin): void
    {
        if (! $admin->exists || blank($admin->id)) {
            throw new RuntimeException('Admin kullanicisi olusturulamadi.');
        }
    }

    private function writeEnvironment(array $data): void
    {
        $path = base_path('.env');

        if (! File::exists($path) || ! File::isWritable($path)) {
            return;
        }

        $values = [
            'APP_NAME' => $data['site_name'],
            'APP_URL' => rtrim($data['site_url'], '/'),
            'APP_LOCALE' => $data['default_language'],
            'APP_TIMEZONE' => $data['default_timezone'],
            'DB_HOST' => $data['db_host'],
            'DB_PORT' => $data['db_port'],
            'DB_DATABASE' => $data['db_database'],
            'DB_USERNAME' => $data['db_username'],
            'DB_PASSWORD' => $data['db_password'] ?? '',
            'MAIL_MAILER' => 'smtp',
            'MAIL_HOST' => $data['mail_host'] ?? '',
            'MAIL_PORT' => $data['mail_port'] ?? '',
            'MAIL_USERNAME' => $data['mail_username'] ?? '',
            'MAIL_PASSWORD' => $data['mail_password'] ?? '',
            'MAIL_SCHEME' => $data['mail_encryption'] ?? '',
            'MAIL_FROM_ADDRESS' => $data['mail_from_address'] ?? '',
        ];

        $content = File::get($path);

        foreach ($values as $key => $value) {
            $content = $this->replaceEnvironmentValue($content, $key, (string) $value);
        }

        File::put($path, $content);
    }

    private function replaceEnvironmentValue(string $content, string $key, string $value): string
    {
        $line = $key . '=' . $this->quoteEnvironmentValue($value);

        if (preg_match('/^' . preg_quote($key, '/') . '=.*/m', $content)) {
            return preg_replace('/^' . preg_quote($key, '/') . '=.*/m', $line, $content) ?? $content;
        }

        return rtrim($content) . PHP_EOL . $line . PHP_EOL;
    }

    private function quoteEnvironmentValue(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (preg_match('/\s|#|"|\'/', $value)) {
            return '"' . str_replace('"', '\\"', $value) . '"';
        }

        return $value;
    }

    private function isInstalled(): bool
    {
        return File::exists(storage_path(self::LOCK_PATH));
    }

    private function createInstallLock(): void
    {
        File::ensureDirectoryExists(storage_path('app'));
        File::put(storage_path(self::LOCK_PATH), now()->toISOString());

        if (! File::exists(storage_path(self::LOCK_PATH))) {
            throw new RuntimeException('installed.lock dosyasi olusturulamadi.');
        }
    }

    private function normalizeStep(int $step): int
    {
        return min(max($step, 1), 7);
    }
}
