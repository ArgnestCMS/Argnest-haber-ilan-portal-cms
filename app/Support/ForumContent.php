<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use Illuminate\Support\Str;

class ForumContent
{
    private const ALLOWED_TAGS = [
        'a',
        'blockquote',
        'br',
        'em',
        'i',
        'iframe',
        'img',
        'li',
        'ol',
        'p',
        'strong',
        'b',
        'ul',
    ];

    public static function sanitize(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);

        $document->loadHTML(
            '<!DOCTYPE html><html><body><div id="forum-content-root">' . mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8') . '</div></body></html>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('forum-content-root');

        if (! $root) {
            return '';
        }

        self::sanitizeChildren($root);

        $clean = '';

        foreach ($root->childNodes as $child) {
            $clean .= $document->saveHTML($child);
        }

        return trim($clean);
    }

    public static function plainText(?string $html): string
    {
        return trim(html_entity_decode(strip_tags(self::sanitize($html)), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    public static function isEmpty(?string $html): bool
    {
        return self::plainText($html) === '' && ! Str::contains(self::sanitize($html), ['<img ', '<iframe ']);
    }

    private static function sanitizeChildren(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if (! $child instanceof DOMElement) {
                continue;
            }

            $tag = Str::lower($child->tagName);

            if (in_array($tag, ['script', 'style'], true)) {
                $child->parentNode?->removeChild($child);
                continue;
            }

            if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                self::unwrapNode($child);
                continue;
            }

            self::sanitizeAttributes($child, $tag);
            self::sanitizeChildren($child);
        }
    }

    private static function unwrapNode(DOMElement $node): void
    {
        $parent = $node->parentNode;

        if (! $parent) {
            return;
        }

        while ($node->firstChild) {
            $parent->insertBefore($node->firstChild, $node);
        }

        $parent->removeChild($node);
    }

    private static function sanitizeAttributes(DOMElement $node, string $tag): void
    {
        $originalAttributes = [];

        foreach (iterator_to_array($node->attributes) as $attribute) {
            $originalAttributes[$attribute->name] = $attribute->value;
            $node->removeAttribute($attribute->name);
        }

        if ($tag === 'a') {
            $href = self::safeUrl($originalAttributes['href'] ?? '');

            if ($href) {
                $node->setAttribute('href', $href);
                $node->setAttribute('rel', 'nofollow noopener noreferrer');
                $node->setAttribute('target', '_blank');
            }

            return;
        }

        if ($tag === 'img') {
            $src = self::safeForumImageUrl($originalAttributes['src'] ?? '');

            if ($src) {
                $node->setAttribute('src', $src);
                $node->setAttribute('alt', '');
                $node->setAttribute('loading', 'lazy');
            } else {
                $node->parentNode?->removeChild($node);
            }

            return;
        }

        if ($tag === 'iframe') {
            $src = self::safeYoutubeEmbedUrl($originalAttributes['src'] ?? '');

            if ($src) {
                $node->setAttribute('src', $src);
                $node->setAttribute('title', 'YouTube video');
                $node->setAttribute('loading', 'lazy');
                $node->setAttribute('allowfullscreen', 'allowfullscreen');
                $node->setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
            } else {
                $node->parentNode?->removeChild($node);
            }
        }
    }

    private static function safeUrl(string $url): ?string
    {
        $url = trim($url);

        if ($url === '' || preg_match('/^\s*javascript:/i', $url)) {
            return null;
        }

        return preg_match('/^(https?:\/\/|mailto:)/i', $url) ? $url : null;
    }

    private static function safeForumImageUrl(string $url): ?string
    {
        $url = trim($url);

        if ($url === '' || ! Str::contains($url, '/storage/forum/')) {
            return null;
        }

        return preg_match('/^(https?:\/\/|\/)/i', $url) ? $url : null;
    }

    private static function safeYoutubeEmbedUrl(string $url): ?string
    {
        $url = trim($url);

        if ($url === '') {
            return null;
        }

        $host = parse_url($url, PHP_URL_HOST);
        $path = parse_url($url, PHP_URL_PATH) ?: '';

        if (! $host || ! in_array(Str::lower($host), ['www.youtube.com', 'youtube.com', 'www.youtube-nocookie.com', 'youtube-nocookie.com'], true)) {
            return null;
        }

        if (! Str::startsWith($path, '/embed/')) {
            return null;
        }

        return preg_replace('/\?.*/', '', $url);
    }
}
