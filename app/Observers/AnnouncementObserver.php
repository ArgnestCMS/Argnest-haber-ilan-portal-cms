<?php

namespace App\Observers;

use App\Helpers\ActivityLogger;
use App\Models\Announcement;

class AnnouncementObserver
{
    /**
     * Handle the Announcement "created" event.
     */
    public function created(Announcement $announcement): void
    {
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
        //
    }

    /**
     * Handle the Announcement "force deleted" event.
     */
    public function forceDeleted(Announcement $announcement): void
    {
        //
    }
}