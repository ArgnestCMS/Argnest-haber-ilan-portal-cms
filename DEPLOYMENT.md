# Production Deployment Guide

Bu rehber Argnest Haber-İlan Portal CMS için canlı ortam hazırlığını özetler.

## Shared Hosting Kurulum

1. Dosyaları hosting hesabına yükleyin.
2. Web root olarak `public` dizinini hedefleyin. Hosting paneli izin vermiyorsa ana domaini `public/index.php` çalışacak şekilde yönlendirin.
3. `.env` dosyasını oluşturup `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://domain.com` ayarlarını girin.
4. Veritabanını oluşturun ve DB bilgilerini `.env` içine yazın.
5. SSH varsa şu komutları çalıştırın:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

SSH yoksa bağımlılıkları yerelde hazırlayıp `vendor`, `public/build` ve proje dosyalarını birlikte yükleyin.

## VPS Kurulum

1. Nginx veya Apache virtual host root değerini `public` dizinine bağlayın.
2. PHP-FPM, MySQL/MariaDB, Redis ve Supervisor kurulumunu tamamlayın.
3. Deploy kullanıcısına proje dizininde gerekli izinleri verin.
4. Composer, npm build, migrate ve cache komutlarını çalıştırın.

## Storage Link

```bash
php artisan storage:link
```

Shared hosting symlink desteklemiyorsa `public/storage` için panel üzerinden symlink oluşturun veya hosting sağlayıcısının desteklediği dosya bağlantı yöntemini kullanın.

## Queue

Varsayılan üretim ayarı:

```env
QUEUE_CONNECTION=database
```

Worker:

```bash
php artisan queue:work database --queue=broadcasts,realtime,notifications,media,safety,default --tries=3 --backoff=5 --timeout=60 --sleep=1
```

VPS üzerinde Supervisor/systemd ile sürekli çalıştırın.

## Cache

Canlı ortamda:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Değişikliklerden sonra:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Redis kullanılıyorsa `CACHE_STORE=redis`, aksi halde `CACHE_STORE=database` güvenli varsayılandır.

## Cron

Sunucu cron tablosuna ekleyin:

```cron
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

## Permissions

Linux örneği:

```bash
chmod -R ug+rw storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

Windows/XAMPP için `storage` ve `bootstrap/cache` dizinlerinin PHP kullanıcısı tarafından yazılabilir olduğundan emin olun.

## Production Env Ayarları

```env
APP_NAME="Argnest Haber-İlan Portal CMS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain.com
APP_LOCALE=tr
APP_FALLBACK_LOCALE=tr

PORTAL_VERSION=v1.0.0
PORTAL_CODENAME=Genesis

LOG_CHANNEL=daily
LOG_LEVEL=info

DB_CONNECTION=mysql
CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_DRIVER=database

MAIL_MAILER=smtp
MYSQLDUMP_PATH=/usr/bin/mysqldump
```

## Son Kontrol

- `/health` readiness kontrolü
- `/up` liveness kontrolü
- Admin login testi
- Haber/ilan oluşturma testi
- Dosya yükleme testi
- Backup alma ve indirme testi
- Queue worker ve cron kontrolü
