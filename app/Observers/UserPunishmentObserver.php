<?php

namespace App\Observers;

use App\Models\UserPunishment;

class UserPunishmentObserver
{
    /**
     * Handle the UserPunishment "created" event.
     */
    public function created(UserPunishment $userPunishment): void
    {
        //
    }

    /**
     * Handle the UserPunishment "updated" event.
     */
    public function updated(UserPunishment $userPunishment): void
    {
        //
    }

    /**
     * Handle the UserPunishment "deleted" event.
     */
    public function deleted(UserPunishment $userPunishment): void
    {
        //
    }

    /**
     * Handle the UserPunishment "restored" event.
     */
    public function restored(UserPunishment $userPunishment): void
    {
        //
    }

    /**
     * Handle the UserPunishment "force deleted" event.
     */
    public function forceDeleted(UserPunishment $userPunishment): void
    {
        //
    }
}
