<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\News;
use App\Models\Video;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FrontendController extends Controller
{
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

    public function home()
    {
        $headlineNews = News::where('is_headline', true)->latest()->take(5)->get();
        $latestNews = News::latest()->take(12)->get();
        $latestAnnouncements = Announcement::latest()->take(12)->get();
$trendingNews = News::where('is_trending', true)
    ->orderByDesc('trend_score')
    ->take(6)
    ->get();

$mostReadNews = News::orderByDesc('views')
    ->take(6)
    ->get();
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        $announcementCategories = Category::where('type', 'announcement')
            ->where('is_active', true)
            ->take(8)
            ->get();

        $latestVideos = Video::where('is_active', true)->latest()->take(6)->get();
        $latestGalleries = Gallery::where('is_active', true)->latest()->take(6)->get();

        $ads = $this->activeAds()->get()->groupBy('position');

        $market = Cache::remember('market_data', 600, function () {
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

        $weather = Cache::remember('weather_data', 600, function () {
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
    'categories',
    'announcementCategories',
    'ads',
    'market',
    'weather',
    'trendingNews',
    'mostReadNews',
));
    }

    public function news()
    {
        $news = News::latest()->paginate(12);

        return view('frontend.news', compact('news'));
    }

    public function announcements()
    {
        $announcements = Announcement::latest()->paginate(12);

        return view('frontend.announcements', compact('announcements'));
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
            $news = News::where('category_id', $category->id)
                ->latest()
                ->paginate(12);

            return view('frontend.news', compact('news', 'category'));
        }

        $announcements = Announcement::where('category_id', $category->id)
            ->latest()
            ->paginate(12);

        return view('frontend.announcements', compact('announcements', 'category'));
    }

    public function search()
    {
        $query = request('q');

        $news = News::query()
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('summary', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");
            })
            ->latest()
            ->paginate(12);

        $announcements = Announcement::query()
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
        $news = News::where('slug', $slug)->firstOrFail();

        $news->recordView();

        $relatedNews = News::where('id', '!=', $news->id)->latest()->take(6)->get();
        $sidebarNews = News::where('id', '!=', $news->id)->latest()->take(8)->get();

        $topAd = $this->activeAds()->where('position', 'top_banner')->first();
        $bottomAd = $this->activeAds()->where('position', 'bottom_banner')->first();
        $leftAd = $this->activeAds()->where('position', 'left_sidebar')->first();
        $rightAd = $this->activeAds()->where('position', 'right_sidebar')->first();
        $sidebarAd = $rightAd;

        return view('frontend.news-detail', compact(
            'news',
            'relatedNews',
            'sidebarNews',
            'topAd',
            'bottomAd',
            'leftAd',
            'rightAd',
            'sidebarAd'
        ));
    }

    public function announcementDetail($slug)
    {
        $announcement = Announcement::where('slug', $slug)->firstOrFail();

        $announcement->increment('views');

        $relatedAnnouncements = Announcement::where('id', '!=', $announcement->id)->latest()->take(6)->get();
        $sidebarAnnouncements = Announcement::where('id', '!=', $announcement->id)->latest()->take(8)->get();

        $topAd = $this->activeAds()->where('position', 'top_banner')->first();
        $bottomAd = $this->activeAds()->where('position', 'bottom_banner')->first();
        $leftAd = $this->activeAds()->where('position', 'left_sidebar')->first();
        $rightAd = $this->activeAds()->where('position', 'right_sidebar')->first();
        $sidebarAd = $rightAd;

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
}