<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\ForumGamification;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'bio' => ['nullable', 'string'],
            'facebook' => ['nullable', 'url'],
            'twitter' => ['nullable', 'url'],
            'instagram' => ['nullable', 'url'],
            'youtube' => ['nullable', 'url'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $user = $request->user();

        $user->name = $request->name;

        $user->email = $request->email;

        $user->bio = $request->bio;

        $user->facebook = $request->facebook;

        $user->twitter = $request->twitter;

        $user->instagram = $request->instagram;

        $user->youtube = $request->youtube;

        if ($request->hasFile('avatar')) {

            $avatarPath = $request->file('avatar')
                ->store('avatars', 'public');

            $user->avatar = $avatarPath;
        }

        $user->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Public Profile Page
     */
    public function show(User $user)
    {
        $user->load('forumBadges');

        $forumStats = [
            'topics' => $user->forumTopics()->where('status', 'published')->count(),
            'posts' => $user->forumPosts()->where('status', 'approved')->count(),
            'likes' => $user->forumTopicLikes()->count(),
            'bookmarks' => $user->forumTopicBookmarks()->count(),
        ];

        $latestForumTopics = $user->forumTopics()
            ->where('status', 'published')
            ->latest()
            ->take(5)
            ->get();

        $levelProgress = ForumGamification::progressToNextLevel($user);

        return view('frontend.profile', compact('user', 'forumStats', 'latestForumTopics', 'levelProgress'));
    }
}
