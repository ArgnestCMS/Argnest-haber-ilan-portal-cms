<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Permissions-Policy',
            'accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()'
        );

        if (! $response->headers->has('Content-Security-Policy')) {
            $response->headers->set('Content-Security-Policy-Report-Only', $this->reportOnlyCsp());
        }

        return $response;
    }

    private function reportOnlyCsp(): string
    {
        return implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'self'",
            "img-src 'self' data: blob: https:",
            "font-src 'self' data: https:",
            "style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://fonts.googleapis.com",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://www.google.com https://www.gstatic.com",
            "connect-src 'self' ws: wss: https:",
            "frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com https://www.google.com",
            "media-src 'self' blob: https:",
            "form-action 'self'",
            'upgrade-insecure-requests',
        ]);
    }
}
