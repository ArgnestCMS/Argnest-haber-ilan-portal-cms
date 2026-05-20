<?php

namespace App\Support;

use App\Models\MediaAsset;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class ContentHtml
{
    public static function render(?string $content): HtmlString
    {
        $html = html_entity_decode($content ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $html = self::removeUnavailableStorageReferences($html);

        $sanitizer = new HtmlSanitizer(
            (new HtmlSanitizerConfig)
                ->allowSafeElements()
                ->allowRelativeLinks()
                ->allowRelativeMedias()
                ->allowLinkSchemes(['http', 'https', 'mailto', 'tel'])
                ->allowMediaSchemes(['http', 'https'])
                ->allowElement('a', ['href', 'target', 'rel', 'title', 'class'])
                ->allowElement('img', ['src', 'alt', 'title', 'width', 'height', 'loading', 'class'])
                ->dropElement('script')
                ->dropElement('style')
                ->dropElement('iframe')
                ->withMaxInputLength(500000),
        );

        return new HtmlString($sanitizer->sanitize($html));
    }

    public static function removeReferencesToStoragePath(string $content, string $storagePath, ?string $publicUrl = null): string
    {
        $urls = array_values(array_filter(array_unique([
            $storagePath,
            '/storage/' . ltrim($storagePath, '/'),
            $publicUrl,
            asset('storage/' . ltrim($storagePath, '/')),
        ])));

        foreach ($urls as $url) {
            $quotedUrl = preg_quote($url, '~');
            $content = preg_replace('~<p>\s*<img\b[^>]*\bsrc=["\']' . $quotedUrl . '["\'][^>]*>\s*</p>~iu', '', $content) ?? $content;
            $content = preg_replace('~<img\b[^>]*\bsrc=["\']' . $quotedUrl . '["\'][^>]*>~iu', '', $content) ?? $content;
            $content = preg_replace('~<p>\s*<a\b[^>]*\bhref=["\']' . $quotedUrl . '["\'][^>]*>.*?</a>\s*</p>~isu', '', $content) ?? $content;
            $content = preg_replace('~<a\b[^>]*\bhref=["\']' . $quotedUrl . '["\'][^>]*>.*?</a>~isu', '', $content) ?? $content;
        }

        return trim($content);
    }

    private static function removeUnavailableStorageReferences(string $html): string
    {
        $html = preg_replace_callback(
            '~<p>\s*(<img\b[^>]*\bsrc=["\']([^"\']+)["\'][^>]*>)\s*</p>~iu',
            fn (array $matches): string => self::isAvailableStorageUrl($matches[2]) ? $matches[0] : '',
            $html,
        ) ?? $html;

        $html = preg_replace_callback(
            '~<img\b[^>]*\bsrc=["\']([^"\']+)["\'][^>]*>~iu',
            fn (array $matches): string => self::isAvailableStorageUrl($matches[1]) ? $matches[0] : '',
            $html,
        ) ?? $html;

        $html = preg_replace_callback(
            '~<p>\s*(<a\b[^>]*\bhref=["\']([^"\']+)["\'][^>]*>.*?</a>)\s*</p>~isu',
            fn (array $matches): string => self::isAvailableStorageUrl($matches[2]) ? $matches[0] : '',
            $html,
        ) ?? $html;

        return preg_replace_callback(
            '~<a\b[^>]*\bhref=["\']([^"\']+)["\'][^>]*>.*?</a>~isu',
            fn (array $matches): string => self::isAvailableStorageUrl($matches[1]) ? $matches[0] : '',
            $html,
        ) ?? $html;
    }

    private static function isAvailableStorageUrl(string $url): bool
    {
        $path = parse_url(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'), PHP_URL_PATH);

        if (! is_string($path)) {
            return true;
        }

        $path = rawurldecode($path);

        if (! str_starts_with($path, '/storage/')) {
            return true;
        }

        $storagePath = ltrim(substr($path, strlen('/storage/')), '/');

        if ($storagePath === '' || str_contains($storagePath, '..')) {
            return false;
        }

        return Storage::disk('public')->exists($storagePath)
            && MediaAsset::query()
                ->where('disk', 'public')
                ->where('visibility', 'public')
                ->where('path', $storagePath)
                ->where('status', 'ready')
                ->exists();
    }
}
