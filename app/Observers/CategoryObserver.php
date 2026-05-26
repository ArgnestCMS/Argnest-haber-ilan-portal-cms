<?php

namespace App\Observers;

use App\Models\Category;
use App\Services\PortalCacheService;

class CategoryObserver
{
    public function created(Category $category): void
    {
        app(PortalCacheService::class)->clearContent();
    }

    public function updated(Category $category): void
    {
        app(PortalCacheService::class)->clearContent();
    }

    public function deleted(Category $category): void
    {
        app(PortalCacheService::class)->clearContent();
    }

    public function restored(Category $category): void
    {
        app(PortalCacheService::class)->clearContent();
    }

    public function forceDeleted(Category $category): void
    {
        app(PortalCacheService::class)->clearContent();
    }
}
