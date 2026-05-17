<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    protected $fillable = [
        'site_title',
        'site_description',
        'site_keywords',

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

        'google_analytics',
        'google_tag_manager',

        'json_ld',
    ];

    protected $casts = [
        'indexing' => 'boolean',
        'robots_index' => 'boolean',
        'robots_follow' => 'boolean',
    ];

    public static function current(): self
    {
        return self::query()->firstOrCreate([]);
    }
}