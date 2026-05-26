<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\ForumCategory;
use App\Models\ForumTag;
use App\Models\ForumTopic;
use App\Models\Gallery;
use App\Models\News;
use App\Models\SeoSetting;
use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class SitemapService
{
    public const CACHE_PREFIX = 'advanced_sitemap:';

    public function index(): string
    {
        return $this->cached('index', fn (): string => $this->xml(
            '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            . $this->sitemap(url('/sitemap-news.xml'))
            . $this->sitemap(url('/sitemap-announcements.xml'))
            . $this->sitemap(url('/sitemap-forum.xml'))
            . $this->sitemap(url('/sitemap-categories.xml'))
            . $this->sitemap(url('/sitemap-media.xml'))
            . '</sitemapindex>'
        ));
    }

    public function news(int $page = 1): string
    {
        return $this->paged('news:' . $page, News::published()->latest('updated_at'), $page, fn (News $news): array => [
            'loc' => url('/haber/' . $news->slug),
            'lastmod' => $news->updated_at,
            'changefreq' => 'daily',
            'priority' => '0.8',
        ]);
    }

    public function announcements(int $page = 1): string
    {
        return $this->paged('announcements:' . $page, Announcement::active()->latest('updated_at'), $page, fn (Announcement $announcement): array => [
            'loc' => url('/ilan/' . $announcement->slug),
            'lastmod' => $announcement->updated_at,
            'changefreq' => 'daily',
            'priority' => '0.8',
        ]);
    }

    public function forum(int $page = 1): string
    {
        return $this->paged('forum:' . $page, ForumTopic::published()->latest('updated_at'), $page, fn (ForumTopic $topic): array => [
            'loc' => route('forum.topics.show', $topic->slug),
            'lastmod' => $topic->updated_at,
            'changefreq' => 'daily',
            'priority' => '0.7',
        ], [
            ['loc' => route('forum.index'), 'lastmod' => now(), 'changefreq' => 'hourly', 'priority' => '0.8'],
        ]);
    }

    public function categories(int $page = 1): string
    {
        return $this->cached('categories:' . $page, function () use ($page): string {
            $items = collect([
                ['loc' => url('/'), 'lastmod' => now(), 'changefreq' => 'hourly', 'priority' => '1.0'],
                ['loc' => url('/haberler'), 'lastmod' => now(), 'changefreq' => 'hourly', 'priority' => '0.9'],
                ['loc' => url('/ilanlar'), 'lastmod' => now(), 'changefreq' => 'hourly', 'priority' => '0.9'],
            ]);

            $categories = Category::where('is_active', true)->latest('updated_at')->forPage($page, 1000)->get()
                ->map(fn (Category $category): array => [
                    'loc' => url('/kategori/' . $category->slug),
                    'lastmod' => $category->updated_at,
                    'changefreq' => 'daily',
                    'priority' => '0.7',
                ]);

            $forumCategories = ForumCategory::active()->latest('updated_at')->forPage($page, 1000)->get()
                ->map(fn (ForumCategory $category): array => [
                    'loc' => route('forum.categories.show', $category->slug),
                    'lastmod' => $category->updated_at,
                    'changefreq' => 'daily',
                    'priority' => '0.7',
                ]);

            $forumTags = ForumTag::active()->latest('updated_at')->forPage($page, 1000)->get()
                ->map(fn (ForumTag $tag): array => [
                    'loc' => route('forum.tags.show', $tag->slug),
                    'lastmod' => $tag->updated_at,
                    'changefreq' => 'daily',
                    'priority' => '0.6',
                ]);

            return $this->urlset($items->merge($categories)->merge($forumCategories)->merge($forumTags)->all());
        });
    }

    public function media(int $page = 1): string
    {
        return $this->cached('media:' . $page, function () use ($page): string {
            $videos = Video::where('is_active', true)->latest('updated_at')->forPage($page, 1000)->get()
                ->map(fn (Video $video): array => [
                    'loc' => route('videos.show', $video->slug),
                    'lastmod' => $video->updated_at,
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ]);

            $galleries = Gallery::where('is_active', true)->latest('updated_at')->forPage($page, 1000)->get()
                ->map(fn (Gallery $gallery): array => [
                    'loc' => route('galleries.show', $gallery->slug),
                    'lastmod' => $gallery->updated_at,
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ]);

            return $this->urlset($videos->merge($galleries)->all());
        });
    }

    public function robots(): string
    {
        $seo = SeoSetting::current();
        $custom = trim((string) $seo->robots_txt);

        $body = $custom !== ''
            ? $custom
            : "User-agent: *\nAllow: /\n\nDisallow: /admin\nDisallow: /login\nDisallow: /register\nDisallow: /dashboard\nDisallow: /profile\nDisallow: /bildirimler\nDisallow: /yorumlarim";

        return trim($body) . "\n\nSitemap: " . url('/sitemap.xml') . "\n";
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_PREFIX . 'index');

        foreach (['news', 'announcements', 'forum', 'categories', 'media'] as $type) {
            for ($page = 1; $page <= 100; $page++) {
                Cache::forget(self::CACHE_PREFIX . $type . ':' . $page);
            }
        }
    }

    private function paged(string $key, Builder $query, int $page, callable $mapper, array $prepend = []): string
    {
        return $this->cached($key, fn (): string => $this->urlset(
            collect($prepend)
                ->merge($query->forPage(max($page, 1), 1000)->get()->map($mapper))
                ->all()
        ));
    }

    private function cached(string $key, callable $callback): string
    {
        $minutes = max((int) (SeoSetting::current()->sitemap_cache_minutes ?? 60), 1);

        return Cache::remember(self::CACHE_PREFIX . $key, now()->addMinutes($minutes), $callback);
    }

    private function sitemap(string $loc): string
    {
        return '<sitemap><loc>' . e($loc) . '</loc><lastmod>' . now()->toAtomString() . '</lastmod></sitemap>';
    }

    private function urlset(array $items): string
    {
        $urls = collect($items)
            ->map(fn (array $item): string => '<url>'
                . '<loc>' . e($item['loc']) . '</loc>'
                . '<lastmod>' . $this->date($item['lastmod'] ?? null) . '</lastmod>'
                . '<changefreq>' . e($item['changefreq'] ?? 'daily') . '</changefreq>'
                . '<priority>' . e($item['priority'] ?? '0.7') . '</priority>'
                . '</url>')
            ->implode('');

        return $this->xml('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . $urls . '</urlset>');
    }

    private function xml(string $body): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' . $body;
    }

    private function date(mixed $date): string
    {
        return ($date instanceof Carbon ? $date : now())->toAtomString();
    }
}
