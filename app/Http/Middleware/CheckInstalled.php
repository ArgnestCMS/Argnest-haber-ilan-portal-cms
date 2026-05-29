<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class CheckInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (File::exists(storage_path('app/installed.lock'))) {
            abort(404);
        }

        return $next($request);
    }
}
