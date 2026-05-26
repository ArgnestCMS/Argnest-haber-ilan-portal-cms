<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserNotificationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $group = $request->string('group')->toString();
        $type = $request->string('type')->toString();

        $notifications = Notification::query()
            ->where('user_id', auth()->id())
            ->when($status === 'unread', fn ($query) => $query->where('is_read', false))
            ->when($status === 'read', fn ($query) => $query->where('is_read', true))
            ->when($group && isset(Notification::TYPE_GROUPS[$group]), fn ($query) => $query->whereIn('type', Notification::TYPE_GROUPS[$group]))
            ->when($type && isset(Notification::TYPE_LABELS[$type]), fn ($query) => $query->where('type', $type))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $counts = [
            'all' => Notification::query()->where('user_id', auth()->id())->count(),
            'unread' => Notification::query()->where('user_id', auth()->id())->where('is_read', false)->count(),
            'read' => Notification::query()->where('user_id', auth()->id())->where('is_read', true)->count(),
            'forum' => Notification::query()->where('user_id', auth()->id())->whereIn('type', Notification::TYPE_GROUPS['forum'])->count(),
            'community' => Notification::query()->where('user_id', auth()->id())->whereIn('type', Notification::TYPE_GROUPS['community'])->count(),
            'moderation' => Notification::query()->where('user_id', auth()->id())->whereIn('type', Notification::TYPE_GROUPS['moderation'])->count(),
        ];

        return view('profile.notifications', [
            'notifications' => $notifications,
            'counts' => $counts,
            'groups' => Notification::TYPE_GROUPS,
            'typeLabels' => Notification::TYPE_LABELS,
            'selectedStatus' => $status,
            'selectedGroup' => $group,
            'selectedType' => $type,
        ]);
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
            ->get()
            ->map(fn (Notification $notification) => [
                'id' => $notification->id,
                'type' => $notification->type,
                'type_label' => $notification->typeLabel(),
                'group' => $notification->group(),
                'title' => $notification->title,
                'message' => $notification->message,
                'url' => $notification->url,
                'read_url' => route('notifications.read', $notification),
                'is_read' => $notification->is_read,
                'created_at' => $notification->created_at?->format('d.m.Y H:i'),
            ]);

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
