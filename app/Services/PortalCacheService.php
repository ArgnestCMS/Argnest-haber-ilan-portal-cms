<?php

namespace App\Services;

use Closure;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Throwable;

class PortalCacheService
{
    private const REGISTRY_KEY = 'portal_cache:keys';

    private static ?string $resolvedStoreName = null;

    public function remember(string $key, string $ttlKey, Closure $callback): mixed
    {
        $this->registerKey($key);

        return $this->store()->remember($key, $this->ttl($ttlKey), $callback);
    }

    public function forget(string $key): void
    {
        $this->store()->forget($key);
    }

    public function clearContent(): int
    {
        return $this->clearByPrefix('portal:');
    }

    public function clearAll(): int
    {
        return $this->clearByPrefix('portal');
    }

    public function storeName(): string
    {
        if (self::$resolvedStoreName !== null) {
            return self::$resolvedStoreName;
        }

        $configuredStore = (string) config('portal_cache.store', 'auto');

        if ($configuredStore !== 'auto') {
            return self::$resolvedStoreName = $configuredStore;
        }

        return self::$resolvedStoreName = $this->redisIsAvailable() ? 'redis' : 'file';
    }

    private function store(): Repository
    {
        return Cache::store($this->storeName());
    }

    private function ttl(string $key): int
    {
        return max(1, (int) config("portal_cache.ttl.{$key}", 300));
    }

    private function redisIsAvailable(): bool
    {
        if (! array_key_exists('redis', config('cache.stores', []))) {
            return false;
        }

        try {
            Cache::store('redis')->put('portal_cache:redis_ping', true, 5);

            return Cache::store('redis')->get('portal_cache:redis_ping') === true;
        } catch (Throwable) {
            return false;
        }
    }

    private function registerKey(string $key): void
    {
        $store = $this->store();
        $keys = $store->get(self::REGISTRY_KEY, []);

        if (! in_array($key, $keys, true)) {
            $keys[] = $key;
            $store->forever(self::REGISTRY_KEY, $keys);
        }
    }

    private function clearByPrefix(string $prefix): int
    {
        $store = $this->store();
        $keys = $store->get(self::REGISTRY_KEY, []);
        $deleted = 0;

        foreach ($keys as $key) {
            if (! str_starts_with($key, $prefix)) {
                continue;
            }

            $store->forget($key);
            $deleted++;
        }

        $remainingKeys = array_values(array_filter(
            $keys,
            fn (string $key): bool => ! str_starts_with($key, $prefix),
        ));

        $store->forever(self::REGISTRY_KEY, $remainingKeys);

        return $deleted;
    }
}
