# Release Checklist

Argnest Haber-İlan Portal CMS v1.0.0 Genesis için sunucu hazır olduğunda uygulanacak kontrol listesi.

## 1. Sunucu Gereksinimleri

- PHP 8.2+
- Composer 2+
- Node.js 18+ ve npm
- MySQL 8+ veya MariaDB 10.6+
- PHP eklentileri: `pdo`, `pdo_mysql`, `openssl`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `curl`, `zip`
- SSL sertifikası
- Cron desteği
- Queue worker desteği: VPS için Supervisor/systemd, shared hosting için panel olanakları
- Yazılabilir dizinler: `storage`, `bootstrap/cache`
- Web root: `public`

## 2. Pakete Dahil Edilecekler

- `app`, `bootstrap`, `config`, `database`, `docs`, `lang`, `public`, `resources`, `routes`, `storage` içindeki `.gitignore` yer tutucuları
- `artisan`
- `composer.json`, `composer.lock`
- `package.json`, `package-lock.json`
- `.env.example`
- `README.md`, `DEPLOYMENT.md`, `CHANGELOG.md`, `RELEASE_CHECKLIST.md`, `LICENSE`, `VERSION`
- `vendor`: sadece SSH/Composer olmayan shared hosting için opsiyonel
- `public/build`: frontend build alındıktan sonra deploy paketinde bulunmalı

## 3. Pakete Dahil Edilmeyecekler

- `.env`
- `node_modules`
- `storage/logs/*`
- `storage/app/backups/*`
- `storage/app/chrome-headless-profile*`
- `storage/app/edge-headless-profile*`
- `storage/app/reports/*`
- `.phpunit.result.cache`, `.phpunit.cache`
- IDE dosyaları ve OS geçici dosyaları
- `vendor`: Composer çalışabilen sunucularda pakete dahil edilmez

## 4. Kurulum Adımları

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan storage:link
```

`.env` içinde `APP_URL`, veritabanı, mail, cache, queue, backup ve portal bilgilerini canlı ortama göre doldurun.

## 5. Install Wizard

- Domain üzerinden `/install` adresini açın.
- Sistem gereksinimi ve yazma izni kontrollerini tamamlayın.
- Veritabanı bağlantısını test edin.
- Site bilgilerini ve ilk admin kullanıcısını oluşturun.
- Kurulum sonunda `storage/app/installed.lock` oluştuğunu kontrol edin.

## 6. Production Cache Komutları

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Route cache komutu çalışmazsa önce route içindeki closure tanımları controller aksiyonlarına taşınmalıdır. Canlıya almadan önce en azından `php artisan route:list` hatasız çalışmalıdır.

## 7. Cron ve Queue

Cron:

```cron
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

Queue:

```bash
php artisan queue:work database --queue=broadcasts,realtime,notifications,media,safety,default --tries=3 --backoff=5 --timeout=60 --sleep=1
```

VPS üzerinde queue worker Supervisor veya systemd ile sürekli çalışmalıdır. Shared hostingde uzun süreli worker yoksa kuyruk kullanan aksiyonlar ayrıca test edilmelidir.

## 8. Son Kontrol

- `/up` ve `/health` çalışıyor
- Admin panel login çalışıyor
- Haber/ilan ekleme çalışıyor
- Dosya yükleme ve `public/storage` erişimi çalışıyor
- Mail gönderimi test edildi
- Backup alma ve indirme test edildi
- Queue worker hata vermiyor
- Cron/scheduler çalışıyor
- `APP_DEBUG=false`
- `.env` public erişim dışında ve release arşivinde yok
