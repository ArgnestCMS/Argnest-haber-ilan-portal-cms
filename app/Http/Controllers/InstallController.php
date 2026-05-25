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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;
use PDO;
use PDOException;
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
        $data = array_replace($this->defaultData(), $request->session()->get('install.data', []));

        validator($data, $this->completionRules())->validate();

        if (! $this->testDatabaseConnection($data)) {
            return redirect()
                ->route('install', ['step' => 3])
                ->withErrors(['db_connection' => 'Kurulum oncesi veritabani baglantisi dogrulanamadi.']);
        }

        $this->writeEnvironment($data);

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
        ]);

        DB::purge('mysql');
        DB::reconnect('mysql');

        Artisan::call('migrate', ['--force' => true]);

        $adminRole = $this->createDefaultRolesAndPermissions();
        $this->createDefaultSettings($data);
        $admin = $this->createAdminUser($data, $adminRole);
        $this->createInstallLock();

        $request->session()->forget('install.data');
        Auth::login($admin);
        $request->session()->regenerate();

        return redirect('/admin/welcome');
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
        SiteSetting::query()->updateOrCreate([], [
            'site_name' => $data['site_name'],
            'site_slogan' => $data['site_description'],
            'forum_enabled' => true,
            'registration_enabled' => true,
            'email_verification_required' => false,
            'maintenance_mode' => false,
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
        ]);

        SeoSetting::query()->updateOrCreate([], [
            'site_title' => $data['site_name'],
            'site_description' => $data['site_description'],
            'default_language' => $data['default_language'],
            'canonical_url' => $data['site_url'],
            'indexing' => true,
            'robots_index' => true,
            'robots_follow' => true,
            'robots_txt' => "User-agent: *\nAllow: /\nSitemap: " . rtrim($data['site_url'], '/') . '/sitemap.xml',
            'sitemap_cache_minutes' => 60,
        ]);

        ThemeSetting::query()->updateOrCreate([], ThemeSetting::DEFAULTS);

        IntegrationSetting::query()->updateOrCreate([], [
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
        ]);
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
        if (File::exists(storage_path(self::LOCK_PATH))) {
            return true;
        }

        try {
            return Schema::hasTable('users') && User::query()->exists();
        } catch (Throwable) {
            return false;
        }
    }

    private function createInstallLock(): void
    {
        File::ensureDirectoryExists(storage_path('app'));
        File::put(storage_path(self::LOCK_PATH), now()->toISOString());
    }

    private function normalizeStep(int $step): int
    {
        return min(max($step, 1), 7);
    }
}
