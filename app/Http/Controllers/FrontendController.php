<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\Comment;
use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\ForumTag;
use App\Models\ForumTopic;
use App\Models\Gallery;
use App\Models\LiveActivity;
use App\Models\LiveChatMessage;
use App\Models\News;
use App\Models\Poll;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\Video;
use App\Services\PortalCacheService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Throwable;

class FrontendController extends Controller
{
    private function cache(): PortalCacheService
    {
        return app(PortalCacheService::class);
    }

    private function activeAds()
    {
        return Advertisement::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    public function home(): RedirectResponse|\Illuminate\View\View
    {
        if (! $this->installationComplete()) {
            return redirect()->route('install');
        }

        $siteSetting = $this->siteSetting();
        $homeModules = $this->homeModules($siteSetting);
        $homeFocus = $this->homeFocus($homeModules);
        $homeModuleSignature = md5($siteSetting?->homeModuleSignature() ?? implode('|', $homeModules));

        $headlineNews = $homeModules['news']
            ? $this->cache()->remember("portal:home:{$homeModuleSignature}:headline-news", 'home', fn () => News::published()->where('is_headline', true)->latest()->take(10)->get())
            : collect();
        $headlineAnnouncements = $homeModules['announcements']
            ? $this->cache()->remember("portal:home:{$homeModuleSignature}:headline-announcements", 'home', fn () => Announcement::active()
            ->where('is_headline', true)
            ->latest()
            ->take(10)
            ->get())
            : collect();
        $headlines = $headlineNews
            ->map(fn (News $news) => [
                'type' => 'news',
                'badge' => 'HABER',
                'title' => $news->title,
                'url' => url('/haber/' . $news->slug),
                'image' => $news->image
                    ? asset('storage/' . (str_contains($news->image, '/') ? $news->image : 'news/' . $news->image))
                    : null,
                'created_at' => $news->created_at,
            ])
            ->concat($headlineAnnouncements->map(fn (Announcement $announcement) => [
                'type' => 'announcement',
                'badge' => 'İLAN',
                'title' => $announcement->title,
                'url' => url('/ilan/' . $announcement->slug),
                'image' => $announcement->image
                    ? asset('storage/' . (str_contains($announcement->image, '/') ? $announcement->image : 'announcements/' . $announcement->image))
                    : null,
                'created_at' => $announcement->created_at,
            ]))
            ->sortByDesc('created_at')
            ->take(10)
            ->values();
        $latestNews = $homeModules['news']
            ? $this->cache()->remember("portal:home:{$homeModuleSignature}:latest-news", 'latest', fn () => News::published()->latest()->take(12)->get())
            : collect();
        $latestAnnouncements = $homeModules['announcements']
            ? $this->cache()->remember("portal:home:{$homeModuleSignature}:latest-announcements", 'latest', fn () => Announcement::active()->latest()->take(12)->get())
            : collect();
        $trendingNews = $homeModules['news']
            ? $this->cache()->remember("portal:home:{$homeModuleSignature}:trending-news", 'trending', fn () => News::published()
            ->where('is_trending', true)
            ->orderByDesc('trend_score')
            ->take(6)
            ->get())
            : collect();

        $mostReadNews = $homeModules['news']
            ? $this->cache()->remember("portal:home:{$homeModuleSignature}:most-read-news", 'popular', fn () => News::published()
            ->orderByDesc('views')
            ->take(6)
            ->get())
            : collect();
        $newsCategories = $homeModules['news']
            ? $this->cache()->remember("portal:home:{$homeModuleSignature}:news-categories", 'categories', fn () => Category::where('type', 'news')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get())
            : collect();

        $announcementCategories = $homeModules['announcements']
            ? $this->cache()->remember("portal:home:{$homeModuleSignature}:announcement-categories", 'categories', fn () => Category::where('type', 'announcement')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get())
            : collect();

        $latestVideos = $homeModules['videos']
            ? $this->cache()->remember("portal:home:{$homeModuleSignature}:latest-videos", 'latest', fn () => Video::where('is_active', true)->latest()->take(6)->get())
            : collect();
        $latestGalleries = $homeModules['galleries']
            ? $this->cache()->remember("portal:home:{$homeModuleSignature}:latest-galleries", 'latest', fn () => Gallery::where('is_active', true)->latest()->take(6)->get())
            : collect();
        $communityData = $homeModules['forum']
            ? $this->cache()->remember("portal:home:{$homeModuleSignature}:community", 'latest', fn () => $this->homeCommunityData())
            : [
                'latestForumTopics' => collect(),
                'trendingForumTopics' => collect(),
                'forumCategories' => collect(),
            ];

        $popupPoll = $homeModules['polls'] ? Poll::popupActive()
            ->with('activeOptions')
            ->latest()
            ->get()
            ->first(fn (Poll $poll) => ! $poll->hasVoteFrom(request()) && $poll->activeOptions->isNotEmpty()) : null;

        $ads = $this->cache()->remember("portal:home:{$homeModuleSignature}:ads", 'ads', fn () => $this->activeAds()->get()->groupBy('position'));

        $market = $this->cache()->remember('portal:external:market', 'external', function () {
            try {
                $rates = Http::timeout(5)
                    ->get('https://api.frankfurter.app/latest?from=USD&to=TRY,EUR')
                    ->json();

                $btc = Http::timeout(5)
                    ->get('https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd')
                    ->json();

                return [
                    'dolar' => $rates['rates']['TRY'] ?? '45.35',
                    'euro' => isset($rates['rates']['EUR']) ? round($rates['rates']['TRY'] / $rates['rates']['EUR'], 2) : '53.52',
                    'altin' => '6875.62',
                    'bist' => '15062.65',
                    'btc' => $btc['bitcoin']['usd'] ?? '81256',
                ];
            } catch (\Exception $e) {
                return [
                    'dolar' => '45.35',
                    'euro' => '53.52',
                    'altin' => '6875.62',
                    'bist' => '15062.65',
                    'btc' => '81256',
                ];
            }
        });

        $weather = $this->cache()->remember('portal:external:weather', 'external', function () {
            try {
                $data = Http::timeout(5)
                    ->get('https://api.open-meteo.com/v1/forecast?latitude=41.0082&longitude=28.9784&current_weather=true')
                    ->json();

                return [
                    'city' => 'İstanbul',
                    'temp' => round($data['current_weather']['temperature'] ?? 19),
                    'status' => 'Açık',
                ];
            } catch (\Exception $e) {
                return [
                    'city' => 'İstanbul',
                    'temp' => 19,
                    'status' => 'Açık',
                ];
            }
        });

        return view('frontend.home', compact(
            'headlineNews',
            'latestNews',
            'latestAnnouncements',
            'latestVideos',
            'latestGalleries',
            'headlines',
            'newsCategories',
            'announcementCategories',
            'ads',
            'market',
            'weather',
            'trendingNews',
            'mostReadNews',
            'popupPoll',
            'homeModules',
            'homeFocus',
            'communityData',
            'siteSetting',
        ));
    }

    private function installationComplete(): bool
    {
        return File::exists(storage_path('app/installed.lock'));
    }

    private function siteSetting(): ?SiteSetting
    {
        try {
            if (! Schema::hasTable('site_settings')) {
                return null;
            }

            return SiteSetting::query()->first();
        } catch (Throwable) {
            return null;
        }
    }

    private function homeModules(?SiteSetting $siteSetting): array
    {
        return [
            'news' => $siteSetting?->homeModuleEnabled('news') ?? true,
            'announcements' => $siteSetting?->homeModuleEnabled('announcements') ?? true,
            'forum' => $siteSetting?->homeModuleEnabled('forum') ?? false,
            'galleries' => $siteSetting?->homeModuleEnabled('galleries') ?? true,
            'videos' => $siteSetting?->homeModuleEnabled('videos') ?? true,
            'polls' => $siteSetting?->homeModuleEnabled('polls') ?? false,
            'breaking_news' => $siteSetting?->homeModuleEnabled('breaking_news') ?? false,
            'announcement_bar' => $siteSetting?->homeModuleEnabled('announcement_bar') ?? false,
        ];
    }

    private function homeFocus(array $modules): string
    {
        return match (true) {
            $modules['news'] && ! $modules['announcements'] => 'news',
            ! $modules['news'] && $modules['announcements'] => 'announcements',
            $modules['news'] && $modules['announcements'] => 'mixed',
            default => 'community',
        };
    }

    private function homeCommunityData(): array
    {
        $topicRelations = ['category', 'user', 'lastPostUser'];

        return [
            'latestForumTopics' => ForumTopic::published()
                ->with($topicRelations)
                ->withCount(['posts' => fn ($query) => $query->where('status', 'approved')])
                ->activeOrder()
                ->take(6)
                ->get(),
            'trendingForumTopics' => ForumTopic::published()
                ->with($topicRelations)
                ->withCount(['posts' => fn ($query) => $query->where('status', 'approved')])
                ->trending()
                ->take(4)
                ->get(),
            'forumCategories' => ForumCategory::active()
                ->withCount(['topics' => fn ($query) => $query->published()])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->take(6)
                ->get(),
        ];
    }

    public function news()
    {
        $page = request()->integer('page', 1);
        $news = $this->cache()->remember("portal:news:list:page:{$page}", 'lists', fn () => News::published()->latest()->paginate(12));

        return view('frontend.news', compact('news'));
    }

    public function announcements()
    {
        $page = request()->integer('page', 1);
        $announcements = $this->cache()->remember("portal:announcements:list:page:{$page}", 'lists', fn () => Announcement::active()->latest()->paginate(12));
        $announcementPortal = $this->announcementPortalData();

        return view('frontend.announcements', compact('announcements', 'announcementPortal'));
    }

    public function videos()
    {
        $videos = Video::where('is_active', true)
            ->latest()
            ->paginate(12);

        return view('frontend.videos', compact('videos'));
    }

    public function videoDetail($slug)
    {
        $video = Video::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $video->increment('views');

        $relatedVideos = Video::where('id', '!=', $video->id)
            ->where('is_active', true)
            ->latest()
            ->take(6)
            ->get();

        $sidebarVideos = Video::where('id', '!=', $video->id)
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        $topAd = $this->activeAds()->where('position', 'top_banner')->first();
        $bottomAd = $this->activeAds()->where('position', 'bottom_banner')->first();
        $leftAd = $this->activeAds()->where('position', 'left_sidebar')->first();
        $rightAd = $this->activeAds()->where('position', 'right_sidebar')->first();
        $sidebarAd = $rightAd;

        return view('frontend.video-detail', compact(
            'video',
            'relatedVideos',
            'sidebarVideos',
            'topAd',
            'bottomAd',
            'leftAd',
            'rightAd',
            'sidebarAd'
        ));
    }

    public function galleries()
    {
        $galleries = Gallery::where('is_active', true)
            ->withCount('images')
            ->latest()
            ->paginate(12);

        return view('frontend.galleries', compact('galleries'));
    }

    public function forum()
    {
        $selectedCategory = request('category')
            ? ForumCategory::active()->whereKey(request('category'))->first()
            : null;

        $selectedTag = request('tag')
            ? ForumTag::active()->where('slug', request('tag'))->first()
            : null;

        return $this->forumView($selectedCategory, $selectedTag);
    }

    public function forumCategory(string $slug)
    {
        $selectedCategory = ForumCategory::active()
            ->where('slug', $slug)
            ->firstOrFail();

        return $this->forumView($selectedCategory);
    }

    public function forumTag(string $slug)
    {
        $selectedTag = ForumTag::active()
            ->where('slug', $slug)
            ->firstOrFail();

        return $this->forumView(null, $selectedTag);
    }

    private function forumView(?ForumCategory $selectedCategory = null, ?ForumTag $selectedTag = null)
    {
        $siteSetting = SiteSetting::first();
        $forumCategories = ForumCategory::active()
            ->withCount([
                'topics' => fn ($query) => $query->published(),
                'topics as solved_topics_count' => fn ($query) => $query->published()->where('is_solved', true),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $topicRelations = ['category', 'user.forumBadges', 'lastPostUser', 'tags'];
        $topicCounts = [
            'likes',
            'bookmarks',
            'posts' => fn ($query) => $query->where('status', 'approved'),
        ];

        $latestForumTopics = ForumTopic::published()
            ->with($topicRelations)
            ->withCount([
                'likes',
                'bookmarks',
                'posts' => fn ($query) => $query->where('status', 'approved'),
            ])
            ->activeOrder()
            ->take(8)
            ->get();

        $trendingForumTopics = ForumTopic::published()
            ->with($topicRelations)
            ->withCount($topicCounts)
            ->trending()
            ->take(5)
            ->get();

        $forumTags = ForumTag::active()
            ->withCount(['topics' => fn ($query) => $query->published()])
            ->orderByDesc('topics_count')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->take(24)
            ->get();

        $discoveryTopics = ForumTopic::published()
            ->with($topicRelations)
            ->withCount($topicCounts)
            ->when(request('q'), function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%")
                        ->orWhereHas('approvedPosts', fn ($postQuery) => $postQuery->where('content', 'like', "%{$search}%"));
                });
            })
            ->when($selectedCategory, fn ($query) => $query->where('forum_category_id', $selectedCategory->id))
            ->when($selectedTag, fn ($query) => $query->whereHas('tags', fn ($tagQuery) => $tagQuery->whereKey($selectedTag->id)))
            ->when(request('filter'), function ($query, $filter) {
                match ($filter) {
                    'trend' => $query->trending(),
                    'solved' => $query->where('is_solved', true)->latest(),
                    'pinned' => $query->where('is_pinned', true)->latest(),
                    'locked' => $query->where('is_locked', true)->latest(),
                    'open' => $query->where('is_locked', false)->where('replies_closed', false)->latest(),
                    default => $query->activeOrder(),
                };
            }, fn ($query) => $query->activeOrder())
            ->paginate(10)
            ->withQueryString();

        $myForumTopics = collect();
        $myForumPosts = collect();
        $myBookmarkedTopics = collect();

        if (auth()->check()) {
            $myForumTopics = ForumTopic::query()
                ->with('category')
                ->where('user_id', auth()->id())
                ->latest()
                ->take(5)
                ->get();

            $myForumPosts = ForumPost::query()
                ->with('topic')
                ->where('user_id', auth()->id())
                ->latest()
                ->take(5)
                ->get();

            $myBookmarkedTopics = ForumTopic::published()
                ->whereHas('bookmarks', fn ($query) => $query->where('user_id', auth()->id()))
                ->latest()
                ->take(5)
                ->get();
        }

        $onlineForumUsersCount = User::query()
            ->where('last_seen_at', '>=', now()->subMinutes(5))
            ->count();

        return view('frontend.forum', compact(
            'siteSetting',
            'forumCategories',
            'latestForumTopics',
            'trendingForumTopics',
            'forumTags',
            'selectedCategory',
            'selectedTag',
            'discoveryTopics',
            'myForumTopics',
            'myForumPosts',
            'myBookmarkedTopics',
            'onlineForumUsersCount'
        ));
    }

    public function forumTopic($slug)
    {
        $siteSetting = SiteSetting::first();

        $topic = ForumTopic::published()
            ->with([
                'category',
                'user.forumBadges',
                'lastPostUser',
                'likes',
                'bookmarks',
                'tags',
                'mediaAssets',
            ])
            ->with(['approvedPosts' => fn ($query) => $query->with(['user', 'mediaAssets'])])
            ->withCount(['likes', 'bookmarks'])
            ->where('slug', $slug)
            ->firstOrFail();

        $topic->increment('views');

        return view('frontend.forum-topic', compact('siteSetting', 'topic'));
    }

    public function liveActivity()
    {
        $siteSetting = SiteSetting::first();
        $activities = LiveActivity::public()
            ->with('user:id,name')
            ->recent()
            ->take(30)
            ->get()
            ->map(fn (LiveActivity $activity) => $activity->toFeedItem());

        return view('frontend.live-activity', compact('siteSetting', 'activities'));
    }

    public function liveChat()
    {
        $siteSetting = SiteSetting::first();
        $messages = LiveChatMessage::approved()
            ->with('user:id,name,last_seen_at,forum_reputation')
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        $onlineUsers = User::query()
            ->where('last_seen_at', '>=', now()->subMinutes(5))
            ->orderBy('name')
            ->take(20)
            ->get(['id', 'name', 'forum_reputation', 'last_seen_at']);

        return view('frontend.live-chat', compact('siteSetting', 'messages', 'onlineUsers'));
    }

    public function galleryDetail($slug)
    {
        $gallery = Gallery::where('slug', $slug)
            ->where('is_active', true)
            ->with(['images' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->firstOrFail();

        $gallery->increment('views');

        $relatedGalleries = Gallery::where('id', '!=', $gallery->id)
            ->where('is_active', true)
            ->latest()
            ->take(6)
            ->get();

        $sidebarGalleries = Gallery::where('id', '!=', $gallery->id)
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        $topAd = $this->activeAds()->where('position', 'top_banner')->first();
        $bottomAd = $this->activeAds()->where('position', 'bottom_banner')->first();
        $leftAd = $this->activeAds()->where('position', 'left_sidebar')->first();
        $rightAd = $this->activeAds()->where('position', 'right_sidebar')->first();
        $sidebarAd = $rightAd;

        return view('frontend.gallery-detail', compact(
            'gallery',
            'relatedGalleries',
            'sidebarGalleries',
            'topAd',
            'bottomAd',
            'leftAd',
            'rightAd',
            'sidebarAd'
        ));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        if ($category->type === 'news') {
            $page = request()->integer('page', 1);
            $news = $this->cache()->remember(
                "portal:news:category:{$category->id}:page:{$page}",
                'lists',
                fn () => News::where('category_id', $category->id)->published()->latest()->paginate(12),
            );

            return view('frontend.news', compact('news', 'category'));
        }

        $page = request()->integer('page', 1);
        $announcements = $this->cache()->remember(
            "portal:announcements:category:{$category->id}:page:{$page}",
            'lists',
            fn () => Announcement::where('category_id', $category->id)->active()->latest()->paginate(12),
        );
        $announcementPortal = $this->announcementPortalData($category);

        return view('frontend.announcements', compact('announcements', 'category', 'announcementPortal'));
    }

    private function announcementPortalData(?Category $selectedCategory = null): array
    {
        $categoryKey = $selectedCategory ? (string) $selectedCategory->id : 'all';

        return $this->cache()->remember("portal:announcements:portal:{$categoryKey}", 'categories', function () use ($selectedCategory): array {
            $headlineQuery = Announcement::active()
                ->where('is_headline', true)
                ->with('category')
                ->latest();

            if ($selectedCategory) {
                $headlineQuery->where('category_id', $selectedCategory->id);
            }

            $categoryBlocks = Category::announcementType()
                ->active()
                ->with(['announcements' => fn ($query) => $query->active()->latest()->take(5)])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
                ->filter(fn (Category $category) => $category->announcements->isNotEmpty())
                ->values();

            return [
                'headlines' => $headlineQuery->take(10)->get(),
                'categoryBlocks' => $selectedCategory
                    ? $categoryBlocks->where('id', $selectedCategory->id)->values()
                    : $categoryBlocks,
                'popular' => Announcement::active()->with('category')->orderByDesc('views')->take(8)->get(),
                'latest' => Announcement::active()->with('category')->latest()->take(8)->get(),
                'categories' => Category::announcementType()
                    ->active()
                    ->withCount(['announcements' => fn ($query) => $query->active()])
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get(),
            ];
        });
    }

    public function search()
    {
        $query = request('q');

        $news = News::query()
            ->published()
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('summary', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");
            })
            ->latest()
            ->paginate(12);

        $announcements = Announcement::query()
            ->active()
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('summary', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%")
                    ->orWhere('institution', 'like', "%{$query}%")
                    ->orWhere('city', 'like', "%{$query}%");
            })
            ->latest()
            ->paginate(12);

        return view('frontend.search', compact(
            'query',
            'news',
            'announcements'
        ));
    }

    public function newsDetail($slug)
    {
        $news = News::published()->where('slug', $slug)->firstOrFail();

        $news->recordView();

        extract($this->newsSidebarData($news->id));

        return view('frontend.news-detail', compact(
            'news',
            'relatedNews',
            'sidebarNews',
            'topAd',
            'bottomAd',
            'leftAd',
            'rightAd',
            'sidebarAd',
            'latestComments'
        ));
    }

    public function announcementDetail($slug)
    {
        $announcement = Announcement::active()->where('slug', $slug)->firstOrFail();

        $announcement->increment('views');

        extract($this->announcementSidebarData($announcement->id));

        return view('frontend.announcement-detail', compact(
            'announcement',
            'relatedAnnouncements',
            'sidebarAnnouncements',
            'topAd',
            'bottomAd',
            'leftAd',
            'rightAd',
            'sidebarAd'
        ));
    }

    private function newsSidebarData(int $newsId): array
    {
        return $this->cache()->remember("portal:sidebar:news-detail:{$newsId}", 'sidebar', function () use ($newsId): array {
            return [
                'relatedNews' => News::published()->where('id', '!=', $newsId)->latest()->take(6)->get(),
                'sidebarNews' => News::published()->where('id', '!=', $newsId)->latest()->take(8)->get(),
                'latestComments' => $this->latestComments(),
                ...$this->sidebarAds(),
            ];
        });
    }

    private function announcementSidebarData(int $announcementId): array
    {
        return $this->cache()->remember("portal:sidebar:announcement-detail:{$announcementId}", 'sidebar', function () use ($announcementId): array {
            return [
                'relatedAnnouncements' => Announcement::active()->where('id', '!=', $announcementId)->latest()->take(6)->get(),
                'sidebarAnnouncements' => Announcement::active()->where('id', '!=', $announcementId)->latest()->take(8)->get(),
                ...$this->sidebarAds(),
            ];
        });
    }

    private function sidebarAds(): array
    {
        return $this->cache()->remember('portal:sidebar:ads', 'ads', function (): array {
            $rightAd = $this->activeAds()->where('position', 'right_sidebar')->first();

            return [
                'topAd' => $this->activeAds()->where('position', 'top_banner')->first(),
                'bottomAd' => $this->activeAds()->where('position', 'bottom_banner')->first(),
                'leftAd' => $this->activeAds()->where('position', 'left_sidebar')->first(),
                'rightAd' => $rightAd,
                'sidebarAd' => $rightAd,
            ];
        });
    }

    private function latestComments()
    {
        return $this->cache()->remember('portal:sidebar:latest-comments', 'sidebar', fn () => Comment::where('status', 'approved')
            ->whereHasMorph('commentable', [
                News::class,
                Announcement::class,
            ])
            ->with(['commentable', 'user'])
            ->latest()
            ->take(5)
            ->get());
    }
}
