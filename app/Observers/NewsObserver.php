<?php

namespace App\Observers;

use App\Helpers\ActivityLogger;
use App\Models\News;
use App\Services\PortalCacheService;

class NewsObserver
{
    /**
     * Handle the News "created" event.
     */
    public function created(News $news): void
    {
        app(PortalCacheService::class)->clearContent();

        ActivityLogger::log(
            'create_news',
            auth()->user()?->name . ' haber ekledi.',
            [
                'news_id' => $news->id,
                'title' => $news->title,
            ]
        );
    }

    /**
     * Handle the News "updated" event.
     */
    public function updated(News $news): void
    {
        if ($this->shouldClearCache($news)) {
            app(PortalCacheService::class)->clearContent();
        }

        ActivityLogger::log(
            'update_news',
            auth()->user()?->name . ' haber düzenledi.',
            [
                'news_id' => $news->id,
                'title' => $news->title,
            ]
        );
    }

    /**
     * Handle the News "deleted" event.
     */
    public function deleted(News $news): void
    {
        app(PortalCacheService::class)->clearContent();

        ActivityLogger::log(
            'delete_news',
            auth()->user()?->name . ' haber sildi.',
            [
                'news_id' => $news->id,
                'title' => $news->title,
            ]
        );
    }

    /**
     * Handle the News "restored" event.
     */
    public function restored(News $news): void
    {
        app(PortalCacheService::class)->clearContent();
    }

    /**
     * Handle the News "force deleted" event.
     */
    public function forceDeleted(News $news): void
    {
        app(PortalCacheService::class)->clearContent();
    }

    private function shouldClearCache(News $news): bool
    {
        return $news->wasChanged([
            'category_id',
            'title',
            'slug',
            'summary',
            'content',
            'image',
            'document',
            'source',
            'publish_date',
            'end_date',
            'news_type',
            'is_headline',
            'is_breaking',
            'comments_enabled',
        ]);
    }
}
