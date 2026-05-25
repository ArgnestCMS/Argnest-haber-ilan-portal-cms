<?php

namespace App\Filament\Pages;

use App\Models\IntegrationSetting;
use App\Models\SiteSetting;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class AdminWelcome extends Page
{
    protected string $view = 'filament.pages.admin-welcome';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRocketLaunch;

    protected static ?string $navigationLabel = 'Baslangic Merkezi';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem Yonetimi';

    protected static ?int $navigationSort = 0;

    protected static ?string $slug = 'welcome';

    protected static ?string $title = 'Argnest Portal Baslangic Merkezi';

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->role === 'super_admin'
            || auth()->user()?->roleModel?->slug === 'super_admin';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Argnest Portal Baslangic Merkezi';
    }

    public function cards(): array
    {
        return [
            ['title' => 'Haber Yonetimi', 'description' => 'Haberleri olustur, duzenle ve yayinla.', 'url' => url('/admin/news')],
            ['title' => 'Ilan Yonetimi', 'description' => 'Ilan kayitlarini ve vitrin akislarini yonet.', 'url' => url('/admin/announcements')],
            ['title' => 'SEO Ayarlari', 'description' => 'Meta, robots ve sitemap ayarlarini duzenle.', 'url' => url('/admin/seo-settings')],
            ['title' => 'Tema Yonetimi', 'description' => 'Renkleri ve gorsel kimligi ayarla.', 'url' => url('/admin/theme-settings')],
            ['title' => 'Backup Sistemi', 'description' => 'Veritabani yedeklerini olustur ve indir.', 'url' => url('/admin/database-backups')],
            ['title' => 'Site Raporlari', 'description' => 'Icerik ve trafik ozetlerini incele.', 'url' => url('/admin/site-reports')],
            ['title' => 'Header Slot Yonetimi', 'description' => 'Ust alan buton ve bannerlarini yonet.', 'url' => url('/admin/header-slots')],
            ['title' => 'Kullanici ve Roller', 'description' => 'Kullanicilar, roller ve yetkileri kontrol et.', 'url' => url('/admin/users')],
            ['title' => 'Moderasyon Sistemi', 'description' => 'Yorum, forum ve topluluk raporlarini incele.', 'url' => url('/admin/community-reports')],
            ['title' => 'Sistem Ayarlari', 'description' => 'Site, uyelik ve ana sayfa modul ayarlarini yonet.', 'url' => url('/admin/site-settings')],
        ];
    }

    public function checklist(): array
    {
        $site = SiteSetting::query()->first();
        $mail = IntegrationSetting::query()->first();

        return [
            ['label' => 'Site adi ayarlandi', 'ok' => filled($site?->site_name)],
            ['label' => 'Mail sistemi yapilandirildi', 'ok' => filled($mail?->mail_host) && filled($mail?->mail_from_address)],
            ['label' => 'Sitemap Google\'a gonderildi', 'ok' => false],
            ['label' => 'Admin hesabi olusturuldu', 'ok' => User::query()->where('role', 'admin')->exists()],
            ['label' => 'Backup testi yapildi', 'ok' => false],
        ];
    }
}
