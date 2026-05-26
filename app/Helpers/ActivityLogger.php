<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Throwable;

class ActivityLogger
{
    public static function log(
        string $action,
        ?string $description = null,
        array $properties = []
    ): void {
        try {
            $request = request();

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'description' => $description,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device' => self::device(),
                'browser' => self::browser(),
                'platform' => self::platform(),
                'url' => $request->fullUrl(),
                'properties' => $properties,
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    public static function device(): string
    {
        $agent = request()->userAgent() ?? '';

        if (str_contains($agent, 'Mobile')) {
            return 'Mobile';
        }

        return 'Desktop';
    }

    public static function browser(): string
    {
        $agent = request()->userAgent() ?? '';

        return match (true) {
            str_contains($agent, 'OPR') || str_contains($agent, 'Opera') => 'Opera',
            str_contains($agent, 'Edg') => 'Edge',
            str_contains($agent, 'Firefox') => 'Firefox',
            str_contains($agent, 'Chrome') => 'Chrome',
            str_contains($agent, 'Safari') => 'Safari',
            default => 'Unknown',
        };
    }

    public static function platform(): string
    {
        $agent = request()->userAgent() ?? '';

        return match (true) {
            str_contains($agent, 'Android') => 'Android',
            str_contains($agent, 'iPhone') || str_contains($agent, 'iPad') => 'iOS',
            str_contains($agent, 'Windows') => 'Windows',
            str_contains($agent, 'Mac') => 'MacOS',
            str_contains($agent, 'Linux') => 'Linux',
            default => 'Unknown',
        };
    }
}