<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\ForumCategory;
use App\Models\ForumTag;
use App\Models\ForumTopic;
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
        $forumCategories = ForumCategory::active()
            ->whereHas('topics', fn ($query) => $query->published())
            ->latest()
            ->get();
        $forumTags = ForumTag::active()
            ->whereHas('topics', fn ($query) => $query->published())
            ->latest()
            ->get();
        $forumTopics = ForumTopic::published()
            ->latest('updated_at')
            ->get();

        return response()
            ->view(
                'frontend.sitemap',
                compact(
                    'news',
                    'announcements',
                    'videos',
                    'galleries',
                    'categories',
                    'forumCategories',
                    'forumTags',
                    'forumTopics'
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
