<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Portal Cache Store
    |--------------------------------------------------------------------------
    |
    | "auto" tries Redis first and falls back to the file cache when Redis is
    | not available. You may also set a concrete Laravel cache store name.
    |
    */

    'store' => env('PORTAL_CACHE_STORE', 'auto'),

    'ttl' => [
        'home' => (int) env('PORTAL_CACHE_HOME_SECONDS', 600),
        'lists' => (int) env('PORTAL_CACHE_LIST_SECONDS', 300),
        'popular' => (int) env('PORTAL_CACHE_POPULAR_SECONDS', 600),
        'trending' => (int) env('PORTAL_CACHE_TRENDING_SECONDS', 600),
        'latest' => (int) env('PORTAL_CACHE_LATEST_SECONDS', 300),
        'categories' => (int) env('PORTAL_CACHE_CATEGORY_SECONDS', 900),
        'sidebar' => (int) env('PORTAL_CACHE_SIDEBAR_SECONDS', 600),
        'ads' => (int) env('PORTAL_CACHE_AD_SECONDS', 300),
        'external' => (int) env('PORTAL_CACHE_EXTERNAL_SECONDS', 600),
        'layout' => (int) env('PORTAL_CACHE_LAYOUT_SECONDS', 300),
    ],
];
