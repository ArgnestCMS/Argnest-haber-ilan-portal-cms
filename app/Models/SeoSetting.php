<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    protected $fillable = [
        'site_title',
        'site_description',
        'site_keywords',
        'default_author',
        'default_language',

        'og_title',
        'og_description',
        'og_image',

        'twitter_title',
        'twitter_description',
        'twitter_image',

        'canonical_url',
        'indexing',

        'robots_index',
        'robots_follow',
        'robots_txt',
        'sitemap_cache_minutes',

        'google_analytics',
        'google_tag_manager',

        'json_ld',
    ];

    protected $casts = [
        'indexing' => 'boolean',
        'robots_index' => 'boolean',
        'robots_follow' => 'boolean',
        'sitemap_cache_minutes' => 'integer',
    ];

    public static function current(): self
    {
        return self::query()->firstOrCreate([]);
    }
}
