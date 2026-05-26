<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LiveChatMessage;
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
        $user->loadCount(['followers', 'following']);

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

        $latestForumPosts = $user->forumPosts()
            ->with('topic')
            ->where('status', 'approved')
            ->latest()
            ->take(5)
            ->get();

        $activityFeed = $this->activityFeedForUser($user);

        $followersPreview = $user->followers()
            ->with('forumBadges')
            ->latest('user_follows.created_at')
            ->take(6)
            ->get();

        $followingPreview = $user->following()
            ->with('forumBadges')
            ->latest('user_follows.created_at')
            ->take(6)
            ->get();

        $isFollowing = auth()->check()
            ? auth()->user()->isFollowing($user)
            : false;

        $levelProgress = ForumGamification::progressToNextLevel($user);

        return view('frontend.profile', compact(
            'user',
            'forumStats',
            'latestForumTopics',
            'latestForumPosts',
            'activityFeed',
            'followersPreview',
            'followingPreview',
            'isFollowing',
            'levelProgress'
        ));
    }

    private function activityFeedForUser(User $user)
    {
        return collect()
            ->merge($user->forumTopics()
                ->where('status', 'published')
                ->latest()
                ->take(6)
                ->get()
                ->map(fn ($topic) => [
                    'title' => 'Forum konusu acti',
                    'message' => $topic->title,
                    'source' => 'forum',
                    'relative_time' => $topic->created_at?->diffForHumans(),
                    'time' => $topic->created_at,
                    'url' => route('forum.topics.show', $topic->slug),
                ]))
            ->merge($user->forumPosts()
                ->with('topic')
                ->where('status', 'approved')
                ->whereHas('topic', fn ($query) => $query->where('status', 'published'))
                ->latest()
                ->take(6)
                ->get()
                ->map(fn ($post) => [
                    'title' => 'Forum cevabi yazdi',
                    'message' => $post->topic?->title ?? 'Forum konusu',
                    'source' => 'forum',
                    'relative_time' => $post->created_at?->diffForHumans(),
                    'time' => $post->created_at,
                    'url' => $post->topic ? route('forum.topics.show', $post->topic->slug) : '#',
                ]))
            ->merge(LiveChatMessage::approved()
                ->where('user_id', $user->id)
                ->latest()
                ->take(6)
                ->get()
                ->map(fn ($message) => [
                    'title' => 'Canli sohbete katildi',
                    'message' => str($message->message)->limit(120)->toString(),
                    'source' => 'chat',
                    'relative_time' => $message->created_at?->diffForHumans(),
                    'time' => $message->created_at,
                    'url' => route('live-chat.index'),
                ]))
            ->sortByDesc('time')
            ->take(12)
            ->values();
    }
}
