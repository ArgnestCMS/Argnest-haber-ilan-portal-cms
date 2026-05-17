<?php

namespace App\Providers;

use App\Helpers\ActivityLogger;

use App\Models\Announcement;
use App\Models\News;
use App\Models\User;

use App\Observers\AnnouncementObserver;
use App\Observers\NewsObserver;
use App\Observers\UserObserver;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | AUTH LOGS
        |--------------------------------------------------------------------------
        */

        Event::listen(Login::class, function ($event) {

            ActivityLogger::log(
                'login',
                $event->user->name . ' sisteme giriş yaptı.'
            );

        });

        Event::listen(Logout::class, function ($event) {

            ActivityLogger::log(
                'logout',
                $event->user?->name . ' sistemden çıkış yaptı.'
            );

        });

        /*
        |--------------------------------------------------------------------------
        | OBSERVERS
        |--------------------------------------------------------------------------
        */

        News::observe(NewsObserver::class);

        Announcement::observe(AnnouncementObserver::class);

        User::observe(UserObserver::class);
    }
}