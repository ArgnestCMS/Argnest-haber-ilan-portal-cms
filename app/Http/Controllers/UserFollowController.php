<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Models\User;
use App\Models\UserFollow;
use Illuminate\Http\RedirectResponse;

class UserFollowController extends Controller
{
    public function toggle(User $user): RedirectResponse
    {
        $follower = auth()->user();

        if ($follower->id === $user->id) {
            return back()->with('error', 'Kendinizi takip edemezsiniz.');
        }

        $follow = UserFollow::query()
            ->where('follower_id', $follower->id)
            ->where('followed_id', $user->id)
            ->first();

        if ($follow) {
            $follow->delete();

            return back()->with('success', 'Takipten cikildi.');
        }

        UserFollow::create([
            'follower_id' => $follower->id,
            'followed_id' => $user->id,
        ]);

        NotificationHelper::sendToUser(
            userId: $user->id,
            type: 'user_followed',
            title: 'Yeni takipci',
            message: $follower->name . ' sizi takip etmeye basladi.',
            url: url('/profil/' . $follower->id),
            data: [
                'follower_id' => $follower->id,
            ]
        );

        return back()->with('success', 'Kullanici takip edildi.');
    }
}
