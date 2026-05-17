<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserNotificationController extends Controller
{
    public function index(): View
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('profile.notifications', compact('notifications'));
    }

    public function unreadCount(): JsonResponse
    {
        return response()->json([
            'count' => Notification::where('user_id', auth()->id())
                ->where('is_read', false)
                ->count(),
        ]);
    }

    public function latest(): JsonResponse
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        return response()->json($notifications);
    }

    public function markAllAsRead(): RedirectResponse
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back();
    }
}