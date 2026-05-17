<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\News;
use App\Models\Video;

class SitemapController extends Controller
{
    public function index()
    {
        $news = News::latest()->get();
        $announcements = Announcement::latest()->get();
        $videos = Video::latest()->get();
        $galleries = Gallery::latest()->get();
        $categories = Category::latest()->get();

        return response()
            ->view(
                'frontend.sitemap',
                compact(
                    'news',
                    'announcements',
                    'videos',
                    'galleries',
                    'categories'
                )
            )
            ->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        return response()
            ->view('frontend.robots')
            ->header('Content-Type', 'text/plain');
    }
}