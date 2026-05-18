<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use App\Models\LiveActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();

        $lastLogin = ActivityLog::query()
            ->where('user_id', $user->id)
            ->where('action', 'login')
            ->latest()
            ->first();

        $currentIp = $request->ip();
        $currentDevice = ActivityLogger::device();
        $currentBrowser = ActivityLogger::browser();
        $currentPlatform = ActivityLogger::platform();

        if ($lastLogin) {
            $isDifferentIp = $lastLogin->ip_address !== $currentIp;
            $isDifferentDevice = $lastLogin->device !== $currentDevice;
            $isDifferentBrowser = $lastLogin->browser !== $currentBrowser;
            $isDifferentPlatform = $lastLogin->platform !== $currentPlatform;

            if ($isDifferentIp || $isDifferentDevice || $isDifferentBrowser || $isDifferentPlatform) {
                ActivityLogger::log(
                    action: 'suspicious_device_login',
                    description: $user->name . ' farklı cihaz/IP/tarayıcı bilgisiyle giriş yaptı.',
                    properties: [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'role' => $user->role ?? null,

                        'old_ip' => $lastLogin->ip_address,
                        'new_ip' => $currentIp,

                        'old_device' => $lastLogin->device,
                        'new_device' => $currentDevice,

                        'old_browser' => $lastLogin->browser,
                        'new_browser' => $currentBrowser,

                        'old_platform' => $lastLogin->platform,
                        'new_platform' => $currentPlatform,
                    ]
                );
            }
        }

        ActivityLogger::log(
            action: 'login',
            description: $user->name . ' sisteme giriş yaptı.',
            properties: [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role ?? null,
            ]
        );

        LiveActivity::record([
            'user' => $user,
            'type' => 'user_login',
            'source' => 'auth',
            'severity' => 'info',
            'title' => 'Kullanıcı giriş yaptı',
            'message' => $user->name . ' topluluğa katıldı.',
            'is_public' => true,
        ]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = auth()->user();

        ActivityLogger::log(
            action: 'logout',
            description: $user?->name . ' sistemden çıkış yaptı.',
            properties: [
                'user_id' => $user?->id,
                'email' => $user?->email,
                'role' => $user?->role ?? null,
            ]
        );

        if ($user) {
            LiveActivity::record([
                'user' => $user,
                'type' => 'user_logout',
                'source' => 'auth',
                'severity' => 'warning',
                'title' => 'Kullanıcı çıkış yaptı',
                'message' => $user->name . ' oturumunu kapattı.',
                'is_public' => true,
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
