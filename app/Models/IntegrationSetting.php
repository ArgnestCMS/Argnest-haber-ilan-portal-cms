<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationSetting extends Model
{
    protected $fillable = [
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'recaptcha_enabled',
        'recaptcha_site_key',
        'recaptcha_secret_key',
        'webpush_enabled',
        'webpush_vapid_public_key',
        'webpush_vapid_private_key',
        'webpush_vapid_subject',
        'google_client_id',
        'google_client_secret',
        'facebook_app_id',
        'facebook_app_secret',
        'captcha_required',
        'mysqldump_path',
    ];

    protected $casts = [
        'mail_port' => 'integer',
        'mail_password' => 'encrypted',
        'recaptcha_enabled' => 'boolean',
        'recaptcha_secret_key' => 'encrypted',
        'webpush_enabled' => 'boolean',
        'webpush_vapid_private_key' => 'encrypted',
        'google_client_secret' => 'encrypted',
        'facebook_app_secret' => 'encrypted',
        'captcha_required' => 'boolean',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }

    public function applyToConfig(): void
    {
        config([
            'mail.default' => $this->mail_mailer ?: config('mail.default'),
            'mail.mailers.smtp.host' => $this->mail_host ?: config('mail.mailers.smtp.host'),
            'mail.mailers.smtp.port' => $this->mail_port ?: config('mail.mailers.smtp.port'),
            'mail.mailers.smtp.username' => $this->mail_username ?: config('mail.mailers.smtp.username'),
            'mail.mailers.smtp.password' => $this->mail_password ?: config('mail.mailers.smtp.password'),
            'mail.mailers.smtp.scheme' => $this->mail_encryption ?: config('mail.mailers.smtp.scheme'),
            'mail.from.address' => $this->mail_from_address ?: config('mail.from.address'),
            'mail.from.name' => $this->mail_from_name ?: config('mail.from.name'),
            'services.recaptcha.enabled' => (bool) $this->recaptcha_enabled,
            'services.recaptcha.site_key' => $this->recaptcha_site_key ?: config('services.recaptcha.site_key'),
            'services.recaptcha.secret_key' => $this->recaptcha_secret_key ?: config('services.recaptcha.secret_key'),
            'services.webpush.enabled' => (bool) $this->webpush_enabled,
            'services.webpush.vapid.subject' => $this->webpush_vapid_subject ?: config('services.webpush.vapid.subject'),
            'services.webpush.vapid.public_key' => $this->webpush_vapid_public_key ?: config('services.webpush.vapid.public_key'),
            'services.webpush.vapid.private_key' => $this->webpush_vapid_private_key ?: config('services.webpush.vapid.private_key'),
            'services.oauth.google.client_id' => $this->google_client_id,
            'services.oauth.google.client_secret' => $this->google_client_secret,
            'services.oauth.facebook.app_id' => $this->facebook_app_id,
            'services.oauth.facebook.app_secret' => $this->facebook_app_secret,
            'security.captcha_required' => (bool) $this->captcha_required,
            'backup.mysqldump_path' => $this->mysqldump_path,
        ]);
    }
}
