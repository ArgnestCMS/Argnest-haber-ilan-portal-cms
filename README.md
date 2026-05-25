# Argnest Haber-İlan Portal CMS

**Argnest Haber-İlan Portal CMS**; haber, ilan, medya, SEO, reklam ve topluluk yönetimini tek yönetim panelinde birleştiren Laravel tabanlı bir içerik yönetim sistemidir.

Alt slogan: **Modern Haber, İlan ve Topluluk Yönetim Sistemi**

## Proje Açıklaması

Sistem; yerel haber portalları, kamu ilan platformları, kurumsal duyuru siteleri ve topluluk odaklı yayın projeleri için hazırlanmıştır. Filament admin paneli ile içerik operasyonları, moderasyon, raporlama, yedekleme ve site ayarları merkezi olarak yönetilir.

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
- MySQL veya MariaDB
- PHP eklentileri: pdo, pdo_mysql, openssl, mbstring, tokenizer, xml, ctype, json, fileinfo
- Yazılabilir `storage` ve `bootstrap/cache` dizinleri

## Kurulum

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

`.env` içinde `APP_URL`, veritabanı, mail, queue, cache ve backup ayarlarını canlı ortama göre düzenleyin.

## Install Wizard

İlk kurulum için `/install` adresi kullanılabilir. Wizard; sistem gereksinimlerini kontrol eder, veritabanı bağlantısını test eder, site ayarlarını ve admin kullanıcısını oluşturur. Kurulum tamamlandığında `storage/app/installed.lock` yazılır ve kurulum ekranı tekrar açılmaz.

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

## SEO Sistemi

SEO sistemi; sayfa başlığı, açıklama, anahtar kelime, canonical URL, robots davranışı, sitemap, Open Graph, Twitter Card ve JSON-LD alanlarını destekler.

## Backup Sistemi

Veritabanı yedekleri `storage/app/backups/database` dizinine alınır. XAMPP için varsayılan `mysqldump.exe` yolu otomatik aranır; gerekirse `MYSQLDUMP_PATH` ile özel yol tanımlanabilir.

## Cache Sistemi

Portal cache sistemi içerik, liste, popüler içerik, sidebar, reklam ve layout parçaları için ayrı TTL değerleri kullanır. `PORTAL_CACHE_STORE=auto` Redis varsa Redis, yoksa Laravel cache fallback davranışı kullanır.

## Forum/Community Sistemi

Forum kategorileri, konular, cevaplar, etiketler, beğeni, yer imi, raporlama, itibar ve canlı topluluk özellikleri bulunur. Moderasyon ve güvenlik ekranları admin panelinden yönetilir.

## Lisans

Bu proje MIT lisansı ile yayınlanmıştır. Detaylar için `LICENSE` dosyasına bakın.

## Güvenlik

- Üretimde `APP_DEBUG=false` kullanılmalıdır.
- `/install` kurulumdan sonra kilitlenir.
- Admin paneli yetki middleware ile korunur.
- Dosya izinleri minimum gerekli erişimle sınırlandırılmalıdır.
- Güvenlik açıkları `PORTAL_SUPPORT_EMAIL` değerinde belirtilen destek adresine bildirilmelidir.

## Katkı

Katkılar için önce issue açılması, değişikliğin kapsamının netleştirilmesi ve ardından küçük, test edilebilir pull request gönderilmesi önerilir.
