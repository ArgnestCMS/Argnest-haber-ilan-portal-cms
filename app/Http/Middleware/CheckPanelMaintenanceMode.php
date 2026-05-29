<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CheckPanelMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('install*')) {
            return $next($request);
        }

        if ($this->shouldPassThrough($request) || $this->userCanBypass($request)) {
            return $next($request);
        }

        $siteSetting = $this->siteSetting();

        if (! (bool) ($siteSetting?->maintenance_mode ?? false)) {
            return $next($request);
        }

        return response()
            ->view('errors.maintenance', [
                'siteSetting' => $siteSetting,
            ], 503)
            ->header('Retry-After', '3600');
    }

    private function shouldPassThrough(Request $request): bool
    {
        return $request->is(
            'admin',
            'admin/*',
            'login',
            'logout',
            'storage/*',
            'build/*',
            'css/*',
            'js/*',
            'images/*',
            'fonts/*',
            'favicon.ico',
            'site.webmanifest',
            'pwa/*',
            'livewire*',
            'livewire/*',
        );
    }

    private function userCanBypass(Request $request): bool
    {
        $user = $request->user();

        return (bool) (
            $user?->isAdmin()
            || $user?->role === 'super_admin'
            || $user?->roleModel?->slug === 'super_admin'
            || $user?->hasPermission('panel_giris')
        );
    }

    private function siteSetting(): ?SiteSetting
    {
        try {
            return SiteSetting::query()->first();
        } catch (Throwable) {
            return null;
        }
    }
}
