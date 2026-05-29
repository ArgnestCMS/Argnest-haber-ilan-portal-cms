<?php

namespace App\Providers;

use App\Helpers\ActivityLogger;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\Comment;
use App\Models\IntegrationSetting;
use App\Models\News;
use App\Models\User;

use App\Observers\AnnouncementObserver;
use App\Observers\CategoryObserver;
use App\Observers\CommentObserver;
use App\Observers\NewsObserver;
use App\Observers\UserObserver;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Throwable;

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
        $this->applyDatabaseBackedSettings();

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

        Comment::observe(CommentObserver::class);

        Category::observe(CategoryObserver::class);

        User::observe(UserObserver::class);
    }

    private function applyDatabaseBackedSettings(): void
    {
        if (! $this->app->runningInConsole() && request()->is('install*')) {
            return;
        }

        try {
            if (! Schema::hasTable('integration_settings')) {
                return;
            }

            IntegrationSetting::query()->first()?->applyToConfig();
        } catch (Throwable) {
            //
        }
    }
}
