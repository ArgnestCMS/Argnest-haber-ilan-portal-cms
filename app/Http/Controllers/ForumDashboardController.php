<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use App\Models\ForumTopic;
use App\Models\SiteSetting;
use Illuminate\View\View;

class ForumDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $user->load('forumBadges');

        $topicRelations = ['category', 'tags'];
        $topicCounts = [
            'likes',
            'bookmarks',
            'posts' => fn ($query) => $query->where('status', 'approved'),
        ];

        $myTopics = ForumTopic::query()
            ->with($topicRelations)
            ->withCount($topicCounts)
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(8, ['*'], 'topics_page');

        $myPosts = ForumPost::query()
            ->with(['topic.category', 'topic.tags'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(8, ['*'], 'posts_page');

        $favoriteTopics = ForumTopic::published()
            ->with($topicRelations)
            ->withCount($topicCounts)
            ->whereHas('bookmarks', fn ($query) => $query->where('user_id', $user->id))
            ->latest()
            ->paginate(8, ['*'], 'favorites_page');

        $likedTopics = ForumTopic::published()
            ->with($topicRelations)
            ->withCount($topicCounts)
            ->whereHas('likes', fn ($query) => $query->where('user_id', $user->id))
            ->latest()
            ->paginate(8, ['*'], 'likes_page');

        $pendingTopics = ForumTopic::query()
            ->with($topicRelations)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->take(6)
            ->get();

        $pendingPosts = ForumPost::query()
            ->with('topic')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->take(6)
            ->get();

        $approvedTopics = ForumTopic::published()
            ->with($topicRelations)
            ->where('user_id', $user->id)
            ->latest()
            ->take(6)
            ->get();

        $approvedPosts = ForumPost::query()
            ->with('topic')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->latest()
            ->take(6)
            ->get();

        $stats = [
            'topics' => ForumTopic::query()->where('user_id', $user->id)->count(),
            'published_topics' => ForumTopic::published()->where('user_id', $user->id)->count(),
            'pending_topics' => ForumTopic::query()->where('user_id', $user->id)->where('status', 'pending')->count(),
            'posts' => ForumPost::query()->where('user_id', $user->id)->count(),
            'approved_posts' => ForumPost::query()->where('user_id', $user->id)->where('status', 'approved')->count(),
            'pending_posts' => ForumPost::query()->where('user_id', $user->id)->where('status', 'pending')->count(),
            'favorites' => $user->forumTopicBookmarks()->count(),
            'likes' => $user->forumTopicLikes()->count(),
            'received_likes' => ForumTopic::query()->where('user_id', $user->id)->withCount('likes')->get()->sum('likes_count'),
            'views' => ForumTopic::query()->where('user_id', $user->id)->sum('views'),
        ];

        return view('frontend.forum-dashboard', [
            'siteSetting' => SiteSetting::query()->first(),
            'user' => $user,
            'stats' => $stats,
            'myTopics' => $myTopics,
            'myPosts' => $myPosts,
            'favoriteTopics' => $favoriteTopics,
            'likedTopics' => $likedTopics,
            'pendingTopics' => $pendingTopics,
            'pendingPosts' => $pendingPosts,
            'approvedTopics' => $approvedTopics,
            'approvedPosts' => $approvedPosts,
        ]);
    }
}
