<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    public function read(Notification $notification): RedirectResponse
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        if (! $notification->is_read) {
            $notification->markAsRead();
        }

        return redirect($notification->url ?? '/dashboard');
    }
}