<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminPanelAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user) {
            abort(403);
        }

        if (! $user->is_active) {
            abort(403);
        }

        if (
            ! $user->isAdmin() &&
            ! $user->hasPermission('panel_giris')
        ) {
            abort(403);
        }

        return $next($request);
    }
}