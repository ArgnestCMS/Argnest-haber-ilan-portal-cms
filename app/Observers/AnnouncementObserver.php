<?php

namespace App\Observers;

use App\Helpers\ActivityLogger;
use App\Models\Announcement;
use App\Services\PortalCacheService;

class AnnouncementObserver
{
    /**
     * Handle the Announcement "created" event.
     */
    public function created(Announcement $announcement): void
    {
        app(PortalCacheService::class)->clearContent();

        ActivityLogger::log(
            'create_announcement',
            auth()->user()?->name . ' ilan ekledi.',
            [
                'announcement_id' => $announcement->id,
                'title' => $announcement->title,
            ]
        );
    }

    /**
     * Handle the Announcement "updated" event.
     */
    public function updated(Announcement $announcement): void
    {
        if ($this->shouldClearCache($announcement)) {
            app(PortalCacheService::class)->clearContent();
        }

        ActivityLogger::log(
            'update_announcement',
            auth()->user()?->name . ' ilan düzenledi.',
            [
                'announcement_id' => $announcement->id,
                'title' => $announcement->title,
            ]
        );
    }

    /**
     * Handle the Announcement "deleted" event.
     */
    public function deleted(Announcement $announcement): void
    {
        app(PortalCacheService::class)->clearContent();

        ActivityLogger::log(
            'delete_announcement',
            auth()->user()?->name . ' ilan sildi.',
            [
                'announcement_id' => $announcement->id,
                'title' => $announcement->title,
            ]
        );
    }

    /**
     * Handle the Announcement "restored" event.
     */
    public function restored(Announcement $announcement): void
    {
        app(PortalCacheService::class)->clearContent();
    }

    /**
     * Handle the Announcement "force deleted" event.
     */
    public function forceDeleted(Announcement $announcement): void
    {
        app(PortalCacheService::class)->clearContent();
    }

    private function shouldClearCache(Announcement $announcement): bool
    {
        return $announcement->wasChanged([
            'category_id',
            'title',
            'slug',
            'summary',
            'content',
            'institution',
            'city',
            'category',
            'publish_date',
            'deadline',
            'source',
            'image',
            'document',
            'is_headline',
            'is_breaking',
            'comments_enabled',
            'is_active',
        ]);
    }
}
