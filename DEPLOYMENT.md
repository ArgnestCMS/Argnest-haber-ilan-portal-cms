# Production Deployment Guide

Bu rehber Argnest Haber-İlan Portal CMS v1.0.0 Genesis için canlı ortam kurulumunu özetler.

## Ortak Ön Hazırlık

1. Sunucuda PHP 8.2+, MySQL/MariaDB, Composer 2 ve gerekli PHP eklentilerini kontrol edin.
2. Domain/SSL hazır olsun ve web root `public` dizinine baksın.
3. `.env.example` dosyasını `.env` olarak kopyalayın.
4. `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://domain.com` değerlerini girin.
5. Veritabanı, mail, cache, queue, `MYSQLDUMP_PATH` ve portal ayarlarını canlı ortama göre doldurun.

## cPanel / Shared Hosting

### SSH Varsa

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### SSH Yoksa

1. Yerelde production bağımlılıklarını hazırlayın: `composer install --no-dev --optimize-autoloader`.
2. Yerelde frontend build alın: `npm install && npm run build`.
3. Proje dosyalarını, `vendor` dizinini ve `public/build` dizinini hosting hesabına yükleyin.
4. Hosting panelinden veritabanı oluşturun ve `.env` bilgilerini girin.
5. Panel izin veriyorsa `public` dizinini domain root yapın. İzin vermiyorsa dosya yapısını hosting sağlayıcısının Laravel yönlendirme dokümanına göre ayarlayın.
6. `storage` ve `bootstrap/cache` yazma izinlerini verin.
7. `php artisan storage:link` çalıştırılamıyorsa panelden `public/storage` için symlink oluşturun veya hosting desteğinden isteyin.

Shared hostingde uzun süre çalışan queue worker desteklenmiyorsa `QUEUE_CONNECTION=database` kalabilir; cron ile Laravel scheduler çalıştırılır. Kritik kuyruk işleri için VPS önerilir.

## VPS

1. Nginx veya Apache virtual host root değerini proje içindeki `public` dizinine bağlayın.
2. PHP-FPM, MySQL/MariaDB, Redis opsiyonel, Composer, Node.js ve Supervisor/systemd kurulumunu tamamlayın.
3. Deploy kullanıcısının proje dizinine, web sunucusu kullanıcısının `storage` ve `bootstrap/cache` dizinlerine gerekli izinleri olduğundan emin olun.
4. Production bağımlılıklarını ve build dosyalarını oluşturun.
5. Migration, storage link ve cache komutlarını çalıştırın.
6. Queue worker için Supervisor/systemd servisi ekleyin.
7. Cron'a Laravel scheduler satırını ekleyin.

## Production Komutları

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

Kod veya ayar değişikliğinden sonra:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Queue

Varsayılan güvenli ayar:

```env
QUEUE_CONNECTION=database
```

Worker:

```bash
php artisan queue:work database --queue=broadcasts,realtime,notifications,media,safety,default --tries=3 --backoff=5 --timeout=60 --sleep=1
```

VPS üzerinde bu komut Supervisor veya systemd ile sürekli çalıştırılmalıdır. Redis kullanılıyorsa `.env` içinde `QUEUE_CONNECTION=redis` ve ilgili Redis ayarları yapılır.

## Cron

Sunucu cron tablosuna ekleyin:

```cron
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

## Storage ve İzinler

```bash
chmod -R ug+rw storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

Windows/XAMPP veya cPanel ortamlarında aynı dizinlerin PHP tarafından yazılabilir olduğunu panel üzerinden kontrol edin.

## Örnek Production Env

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

- `/up` liveness kontrolü
- `/health` readiness kontrolü
- `/install` wizard veya manuel admin oluşturma akışı
- Admin login testi
- Haber/ilan oluşturma testi
- Dosya yükleme ve `public/storage` testi
- Mail gönderim testi
- Backup alma ve indirme testi
- Queue worker kontrolü
- Cron/scheduler kontrolü
