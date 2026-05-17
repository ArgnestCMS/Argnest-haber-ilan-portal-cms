<?php

namespace App\Http\Controllers;

use App\Models\WorkSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WorkSessionController extends Controller
 {
   private function allowed(): void
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
 }

    private function device(): string
    {
        $agent = request()->userAgent();

        return str_contains($agent, 'Mobile') ? 'Mobile' : 'Desktop';
    }

    private function browser(): string
    {
        $agent = request()->userAgent();

        return match (true) {
            str_contains($agent, 'Firefox') => 'Firefox',
            str_contains($agent, 'Chrome') => 'Chrome',
            str_contains($agent, 'Safari') => 'Safari',
            str_contains($agent, 'Edge') => 'Edge',
            default => 'Unknown',
        };
    }

    private function platform(): string
    {
        $agent = request()->userAgent();

        return match (true) {
            str_contains($agent, 'Windows') => 'Windows',
            str_contains($agent, 'Mac') => 'MacOS',
            str_contains($agent, 'Linux') => 'Linux',
            str_contains($agent, 'Android') => 'Android',
            str_contains($agent, 'iPhone') => 'iOS',
            default => 'Unknown',
        };
    }

    private function finishActiveSessions(): void
    {
        $sessions = WorkSession::where('user_id', auth()->id())
            ->where('status', 'active')
            ->get();

        foreach ($sessions as $session) {
            $endedAt = now();

            $session->update([
                'status' => 'completed',
                'ended_at' => $endedAt,
                'duration_minutes' => $session->started_at->diffInMinutes($endedAt),
            ]);
        }
    }

    public function startWork(Request $request): RedirectResponse
    {
        $this->allowed();

        $this->finishActiveSessions();

        WorkSession::create([
            'user_id' => auth()->id(),
            'type' => 'work',
            'status' => 'active',
            'started_at' => now(),
            'ip_address' => $request->ip(),
            'device' => $this->device(),
            'browser' => $this->browser(),
            'platform' => $this->platform(),
            'note' => 'Mesai başladı',
        ]);

        return back();
    }

    public function startBreak(Request $request): RedirectResponse
    {
        $this->allowed();

        $this->finishActiveSessions();

        WorkSession::create([
            'user_id' => auth()->id(),
            'type' => 'break',
            'status' => 'active',
            'started_at' => now(),
            'ip_address' => $request->ip(),
            'device' => $this->device(),
            'browser' => $this->browser(),
            'platform' => $this->platform(),
            'note' => 'Molaya çıktı',
        ]);

        return back();
    }

    public function startLunch(Request $request): RedirectResponse
    {
        $this->allowed();

        $this->finishActiveSessions();

        WorkSession::create([
            'user_id' => auth()->id(),
            'type' => 'lunch',
            'status' => 'active',
            'started_at' => now(),
            'ip_address' => $request->ip(),
            'device' => $this->device(),
            'browser' => $this->browser(),
            'platform' => $this->platform(),
            'note' => 'Yemeğe çıktı',
        ]);

        return back();
    }

    public function backToWork(Request $request): RedirectResponse
    {
        $this->allowed();

        $this->finishActiveSessions();

        WorkSession::create([
            'user_id' => auth()->id(),
            'type' => 'work',
            'status' => 'active',
            'started_at' => now(),
            'ip_address' => $request->ip(),
            'device' => $this->device(),
            'browser' => $this->browser(),
            'platform' => $this->platform(),
            'note' => 'Aktif mesaiye döndü',
        ]);

        return back();
    }

    public function endWork(): RedirectResponse
    {
        $this->allowed();

        $this->finishActiveSessions();

        return back();
    }
}