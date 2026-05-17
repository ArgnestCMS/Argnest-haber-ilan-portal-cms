<?php

namespace App\Observers;

use App\Helpers\ActivityLogger;
use App\Models\News;

class NewsObserver
{
    /**
     * Handle the News "created" event.
     */
    public function created(News $news): void
    {
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
        //
    }

    /**
     * Handle the News "force deleted" event.
     */
    public function forceDeleted(News $news): void
    {
        //
    }
}