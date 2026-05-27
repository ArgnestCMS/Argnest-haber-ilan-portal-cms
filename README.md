# Argnest Haber-İlan Portal CMS

**Argnest Haber-İlan Portal CMS**; haber, ilan, medya, SEO, reklam ve topluluk yönetimini tek yönetim panelinde birleştiren Laravel tabanlı bir içerik yönetim sistemidir.

Alt slogan: **Modern Haber, İlan ve Topluluk Yönetim Sistemi**

## Sürüm

- Ürün: Argnest Haber-İlan Portal CMS
- Sürüm: v1.0.0
- Kod adı: Genesis
- Durum: Sunucuya yüklenmeye hazır release paketi

## Özellikler

- Haber, ilan, kategori, şehir ve kurum yönetimi
- Galeri, video, reklam ve header slot yönetimi
- Üyelik, rol ve yetki sistemi
- Forum, canlı sohbet, özel mesaj ve topluluk raporları
- SEO meta, robots.txt, sitemap ve schema altyapısı
- Veritabanı backup alma, indirme ve silme ekranları
- Portal cache temizleme ve TTL ayarları
- PWA, push notification ve realtime entegrasyon hazırlığı
- Kurulum sihirbazı ve install lock mekanizması

## Sistem Gereksinimleri

- PHP 8.2+
- Composer 2+
- Node.js 18+ ve npm
- MySQL 8+ veya MariaDB 10.6+
- PHP eklentileri: `pdo`, `pdo_mysql`, `openssl`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `curl`, `zip`
- Yazılabilir dizinler: `storage`, `bootstrap/cache`
- Tavsiye edilen: SSL sertifikası, SSH erişimi, cron desteği, queue worker desteği

## Kurulum

1. Dosyaları sunucuya yükleyin ve web root olarak `public` dizinini hedefleyin.
2. `.env.example` dosyasını `.env` olarak kopyalayın.
3. `.env` içinde `APP_URL`, veritabanı, mail, cache, queue ve backup ayarlarını canlı ortama göre düzenleyin.
4. Bağımlılıkları ve frontend dosyalarını hazırlayın.
5. Uygulama anahtarını, migrationları, storage linkini ve production cache dosyalarını oluşturun.

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Shared hostingde SSH yoksa `vendor` ve `public/build` dizinleri yerelde hazırlanıp paketle birlikte yüklenebilir. Bu durumda `vendor` release arşivine opsiyonel olarak dahil edilir; Git deposunda tutulmaz.

## Install Wizard

İlk kurulum için `/install` adresi kullanılabilir. Wizard sistem gereksinimlerini kontrol eder, veritabanı bağlantısını test eder, site ayarlarını ve admin kullanıcısını oluşturur. Kurulum tamamlandığında `storage/app/installed.lock` yazılır ve kurulum ekranı tekrar açılmaz.

## Kullanılan Teknolojiler

- Laravel 12
- Filament 5
- Tailwind CSS
- Alpine.js
- Laravel Reverb
- Predis / Redis desteği
- Minishlink Web Push

## Admin Özellikleri

- Filament tabanlı yönetim paneli
- Başlangıç merkezi ve kullanım kılavuzu
- İçerik, medya, reklam, tema ve sistem ayarları
- Rol, yetki, kullanıcı ve moderasyon ekranları
- Site raporları ve aktivite kayıtları

## Operasyon Notları

- Cron: `* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1`
- Queue: `php artisan queue:work database --queue=broadcasts,realtime,notifications,media,safety,default --tries=3 --backoff=5 --timeout=60 --sleep=1`
- Sağlık kontrolleri: `/up` ve `/health`
- Backup dizini: `storage/app/backups/database`

## Release Paketine Alınmayacaklar

- `.env`
- `node_modules`
- `storage/logs/*`
- `storage/app/backups/*`
- `.phpunit.result.cache`, `.phpunit.cache`
- Headless browser/test profilleri ve geçici raporlar
- `vendor` varsayılan olarak alınmaz; SSH olmayan hosting için opsiyonel dahil edilebilir.

## Lisans

Bu proje MIT lisansı ile yayınlanmıştır. Detaylar için `LICENSE` dosyasına bakın.

## Güvenlik

- Üretimde `APP_ENV=production` ve `APP_DEBUG=false` kullanılmalıdır.
- `/install` kurulumdan sonra kilitlenir.
- Admin paneli yetki middleware ile korunur.
- Dosya izinleri minimum gerekli erişimle sınırlandırılmalıdır.
- Güvenlik açıkları `PORTAL_SUPPORT_EMAIL` değerinde belirtilen destek adresine bildirilmelidir.

- ## 🎬 Tanıtım Videosu

📺 YouTube:
https://www.youtube.com/watch?v=wgMmliALmUw

