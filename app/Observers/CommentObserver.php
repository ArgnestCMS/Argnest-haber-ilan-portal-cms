<?php

namespace App\Observers;

use App\Models\Comment;
use App\Services\PortalCacheService;

class CommentObserver
{
    /**
     * Handle the Comment "created" event.
     */
    public function created(Comment $comment): void
    {
        app(PortalCacheService::class)->clearContent();
    }

    /**
     * Handle the Comment "updated" event.
     */
    public function updated(Comment $comment): void
    {
        app(PortalCacheService::class)->clearContent();
    }

    /**
     * Handle the Comment "deleted" event.
     */
    public function deleted(Comment $comment): void
    {
        app(PortalCacheService::class)->clearContent();
    }

    /**
     * Handle the Comment "restored" event.
     */
    public function restored(Comment $comment): void
    {
        app(PortalCacheService::class)->clearContent();
    }

    /**
     * Handle the Comment "force deleted" event.
     */
    public function forceDeleted(Comment $comment): void
    {
        app(PortalCacheService::class)->clearContent();
    }
}
