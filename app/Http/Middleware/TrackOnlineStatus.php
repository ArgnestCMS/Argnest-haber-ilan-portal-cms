<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackOnlineStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('install*')) {
            return $next($request);
        }

        if (auth()->check()) {

            $user = auth()->user();

            $user->update([

                'last_seen_at' => now(),

                'last_ip_address' => $request->ip(),

                'last_device' => $request->header('User-Agent'),

                'last_browser' => $this->detectBrowser(
                    $request->header('User-Agent')
                ),

                'last_platform' => $this->detectPlatform(
                    $request->header('User-Agent')
                ),

            ]);
        }

        return $next($request);
    }

    private function detectBrowser(?string $userAgent): string
    {
        if (! $userAgent) {
            return 'Unknown';
        }

        return match (true) {

            str_contains($userAgent, 'Chrome') => 'Chrome',

            str_contains($userAgent, 'Firefox') => 'Firefox',

            str_contains($userAgent, 'Safari') => 'Safari',

            str_contains($userAgent, 'Edge') => 'Edge',

            str_contains($userAgent, 'Opera') => 'Opera',

            default => 'Unknown',
        };
    }

    private function detectPlatform(?string $userAgent): string
    {
        if (! $userAgent) {
            return 'Unknown';
        }

        return match (true) {

            str_contains($userAgent, 'Windows') => 'Windows',

            str_contains($userAgent, 'Linux') => 'Linux',

            str_contains($userAgent, 'Macintosh') => 'MacOS',

            str_contains($userAgent, 'Android') => 'Android',

            str_contains($userAgent, 'iPhone') => 'iOS',

            default => 'Unknown',
        };
    }
}
