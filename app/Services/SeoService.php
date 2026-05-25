<?php

namespace App\Services;

use App\Models\SeoSetting;
use App\Models\SiteSetting;
use Illuminate\Support\Str;

class SeoService
{
    public function meta(array $overrides = []): array
    {
        $seo = SeoSetting::current();
        $site = SiteSetting::first();
        $siteName = $site?->site_name ?? config('app.name');

        $title = trim((string) ($this->value($overrides, 'title') ?? $seo->site_title ?? $site?->seo_title ?? $siteName));

        if ($title !== '' && ! Str::contains($title, $siteName)) {
            $title .= ' | ' . $siteName;
        }

        $description = $this->limit(
            $this->value($overrides, 'description')
                ?? $seo->site_description
                ?? $site?->seo_description
                ?? 'Guncel haberler, kamu ilanlari, personel alimlari ve son dakika gelismeleri.',
            160,
        );

        $keywords = trim((string) ($this->value($overrides, 'keywords') ?? $seo->site_keywords ?? $site?->seo_keywords ?? 'haberler, ilanlar, kamu ilanlari'));
        $image = $this->value($overrides, 'image') ?? $this->imageUrl($seo->og_image ?: $seo->twitter_image);
        $canonical = $this->value($overrides, 'canonical') ?? ($seo->canonical_url ?: url()->current());
        $robots = $this->value($overrides, 'robots') ?? (($seo->robots_index ?? true) ? 'index' : 'noindex') . ', ' . (($seo->robots_follow ?? true) ? 'follow' : 'nofollow');
        $author = $this->value($overrides, 'author') ?? $seo->default_author ?? $siteName;
        $language = $this->value($overrides, 'language') ?? $seo->default_language ?? app()->getLocale();

        return [
            'site_name' => $siteName,
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'canonical' => $canonical,
            'robots' => $robots,
            'author' => $author,
            'language' => $language,
            'image' => $image ?: asset('default-og.jpg'),
            'og_title' => $this->value($overrides, 'og_title') ?? $seo->og_title ?? $title,
            'og_description' => $this->value($overrides, 'og_description') ?? $seo->og_description ?? $description,
            'og_image' => $this->value($overrides, 'og_image') ?? $image ?: asset('default-og.jpg'),
            'og_url' => $this->value($overrides, 'og_url') ?? $canonical,
            'og_type' => $this->value($overrides, 'og_type') ?? 'website',
            'twitter_title' => $this->value($overrides, 'twitter_title') ?? $seo->twitter_title ?? $title,
            'twitter_description' => $this->value($overrides, 'twitter_description') ?? $seo->twitter_description ?? $description,
            'twitter_image' => $this->value($overrides, 'twitter_image') ?? $this->imageUrl($seo->twitter_image) ?: ($image ?: asset('default-og.jpg')),
        ];
    }

    public function organizationSchema(?SiteSetting $site = null): array
    {
        $site ??= SiteSetting::first();
        $siteName = $site?->site_name ?? config('app.name');

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $siteName,
            'url' => url('/'),
            'logo' => $site?->logo ? asset('storage/' . $site->logo) : asset('favicon.png'),
        ];
    }

    public function websiteSchema(?SiteSetting $site = null): array
    {
        $siteName = $site?->site_name ?? config('app.name');

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/arama') . '?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    public function breadcrumbSchema(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)
                ->values()
                ->map(fn (array $item, int $index): array => [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $item['name'],
                    'item' => $item['url'],
                ])
                ->all(),
        ];
    }

    private function imageUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://']) ? $path : asset('storage/' . $path);
    }

    private function value(array $values, string $key): ?string
    {
        $value = $values[$key] ?? null;

        return filled($value) ? (string) $value : null;
    }

    private function limit(?string $value, int $limit): string
    {
        return Str::limit(trim(strip_tags((string) $value)), $limit, '');
    }
}
