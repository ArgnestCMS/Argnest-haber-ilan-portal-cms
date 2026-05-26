<?php

namespace App\Http\Controllers;

use App\Services\SitemapService;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(SitemapService $sitemap): Response
    {
        return $this->xml($sitemap->index());
    }

    public function news(SitemapService $sitemap, int $page = 1): Response
    {
        return $this->xml($sitemap->news($page));
    }

    public function announcements(SitemapService $sitemap, int $page = 1): Response
    {
        return $this->xml($sitemap->announcements($page));
    }

    public function forum(SitemapService $sitemap, int $page = 1): Response
    {
        return $this->xml($sitemap->forum($page));
    }

    public function categories(SitemapService $sitemap, int $page = 1): Response
    {
        return $this->xml($sitemap->categories($page));
    }

    public function media(SitemapService $sitemap, int $page = 1): Response
    {
        return $this->xml($sitemap->media($page));
    }

    public function robots(SitemapService $sitemap): Response
    {
        return response($sitemap->robots(), 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    private function xml(string $content): Response
    {
        return response($content, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
