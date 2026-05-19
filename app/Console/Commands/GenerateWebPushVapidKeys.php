<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Minishlink\WebPush\VAPID;

class GenerateWebPushVapidKeys extends Command
{
    protected $signature = 'push:vapid-generate {--force : Replace existing VAPID keys in .env}';

    protected $description = 'Generate WebPush VAPID keys and write local test settings to .env.';

    public function handle(): int
    {
        $envPath = base_path('.env');

        if (! File::exists($envPath)) {
            $this->error('.env dosyasi bulunamadi.');

            return self::FAILURE;
        }

        $env = File::get($envPath);
        $existingPublicKey = $this->readEnvValue($env, 'WEBPUSH_VAPID_PUBLIC_KEY');
        $existingPrivateKey = $this->readEnvValue($env, 'WEBPUSH_VAPID_PRIVATE_KEY');

        if (! $this->option('force') && ($existingPublicKey || $existingPrivateKey)) {
            $this->warn('WEBPUSH VAPID key degerleri zaten var. Yenilemek icin --force kullanin.');

            return self::SUCCESS;
        }

        $keys = VAPID::createVapidKeys();
        $subject = config('app.url') ?: 'http://localhost';

        $env = $this->setEnvValue($env, 'WEBPUSH_VAPID_SUBJECT', $subject);
        $env = $this->setEnvValue($env, 'WEBPUSH_VAPID_PUBLIC_KEY', $keys['publicKey']);
        $env = $this->setEnvValue($env, 'WEBPUSH_VAPID_PRIVATE_KEY', $keys['privateKey']);

        if ($this->readEnvValue($env, 'WEBPUSH_ENABLED') === null) {
            $env = $this->setEnvValue($env, 'WEBPUSH_ENABLED', 'false');
        }

        File::put($envPath, $env);

        $this->info('VAPID key degerleri .env local test ayarlarina yazildi.');
        $this->warn('WEBPUSH_ENABLED=true yapilmadi; local test icin hazir oldugunda manuel olarak acilmali.');

        return self::SUCCESS;
    }

    private function readEnvValue(string $env, string $key): ?string
    {
        if (! preg_match('/^'.preg_quote($key, '/').'=(.*)$/m', $env, $matches)) {
            return null;
        }

        return trim($matches[1], "\"'");
    }

    private function setEnvValue(string $env, string $key, string $value): string
    {
        $line = $key.'='.$this->formatEnvValue($value);

        if (preg_match('/^'.preg_quote($key, '/').'=.*$/m', $env)) {
            return preg_replace('/^'.preg_quote($key, '/').'=.*$/m', $line, $env) ?? $env;
        }

        return rtrim($env).PHP_EOL.$line.PHP_EOL;
    }

    private function formatEnvValue(string $value): string
    {
        if ($value === 'false' || $value === 'true' || preg_match('/^[A-Za-z0-9_\-:.\/]+$/', $value)) {
            return $value;
        }

        return '"'.str_replace('"', '\"', $value).'"';
    }
}
