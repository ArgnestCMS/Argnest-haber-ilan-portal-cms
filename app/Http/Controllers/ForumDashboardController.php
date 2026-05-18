<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use App\Models\ForumQuest;
use App\Models\ForumTopic;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\ForumGamification;
use Illuminate\View\View;

class ForumDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $user->load('forumBadges');
        ForumGamification::ensureDefaultQuests();

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

        $levelProgress = ForumGamification::progressToNextLevel($user);
        $today = now()->toDateString();

        $todayQuests = ForumQuest::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get()
            ->map(function (ForumQuest $quest) use ($user, $today) {
                $pivot = $user->forumQuests()
                    ->where('forum_quest_id', $quest->id)
                    ->wherePivot('tracked_on', $today)
                    ->first()?->pivot;

                $progress = min($quest->target, (int) ($pivot?->progress ?? 0));

                return [
                    'name' => $quest->name,
                    'target' => $quest->target,
                    'progress' => $progress,
                    'percent' => $quest->target > 0 ? min(100, (int) round(($progress / $quest->target) * 100)) : 0,
                    'is_completed' => (bool) ($pivot?->is_completed ?? false),
                    'xp_reward' => $quest->xp_reward,
                    'reputation_reward' => $quest->reputation_reward,
                ];
            });

        $recentReputationEvents = $user->forumReputationEvents()
            ->latest()
            ->take(8)
            ->get();

        $leaderboard = User::query()
            ->orderByDesc('forum_xp')
            ->orderByDesc('forum_reputation')
            ->take(10)
            ->get(['id', 'name', 'forum_reputation', 'forum_xp', 'forum_level']);

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
            'levelProgress' => $levelProgress,
            'todayQuests' => $todayQuests,
            'recentReputationEvents' => $recentReputationEvents,
            'leaderboard' => $leaderboard,
        ]);
    }
}
