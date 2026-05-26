<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | GENEL
        |--------------------------------------------------------------------------
        */

        'site_name',
        'site_slogan',

        'logo',
        'favicon',

        'site_announcement_enabled',
        'site_announcement_icon',
        'site_announcement_text',
        'site_announcement_starts_at',
        'site_announcement_ends_at',

        /*
        |--------------------------------------------------------------------------
        | ÜYELİK
        |--------------------------------------------------------------------------
        */

        'registration_enabled',
        'email_verification_required',
        'membership_agreement',
        'privacy_policy',
        'community_rules',

        /*
        |--------------------------------------------------------------------------
        | TOPLULUK & CANLI SİSTEM
        |--------------------------------------------------------------------------
        */

        'forum_enabled',

        'live_chat_enabled',

        'live_stream_enabled',
        'live_stream_title',
        'live_stream_description',
        'live_stream_url',

        'live_announcement_enabled',
        'live_announcement_text',
        'live_announcement_type',

        /*
        |--------------------------------------------------------------------------
        | SEO
        |--------------------------------------------------------------------------
        */

        'seo_title',
        'seo_description',
        'seo_keywords',

        /*
        |--------------------------------------------------------------------------
        | İLETİŞİM
        |--------------------------------------------------------------------------
        */

        'email',
        'phone',
        'address',

        /*
        |--------------------------------------------------------------------------
        | SOSYAL MEDYA
        |--------------------------------------------------------------------------
        */

        'facebook',
        'twitter',
        'instagram',
        'youtube',
        'telegram',

        /*
        |--------------------------------------------------------------------------
        | KOD ALANLARI
        |--------------------------------------------------------------------------
        */

        'header_scripts',
        'footer_scripts',
        'google_analytics',
        'adsense_code',

        /*
        |--------------------------------------------------------------------------
        | FOOTER
        |--------------------------------------------------------------------------
        */

        'footer_about',
        'footer_copyright',

        /*
        |--------------------------------------------------------------------------
        | DURUM
        |--------------------------------------------------------------------------
        */

        'maintenance_mode',
        'maintenance_message',
        'maintenance_ends_at',
        'auto_punishment_enabled',
        'home_news_enabled',
        'home_announcements_enabled',
        'home_forum_enabled',
        'home_galleries_enabled',
        'home_videos_enabled',
        'home_polls_enabled',
        'home_breaking_news_enabled',
        'home_announcement_bar_enabled',
        'weather_city',
        'weather_temperature_fallback',
        'weather_status_fallback',
        'weather_enabled',
        'weather_local_fallback_city',
        'weather_cache_minutes',

    ];

    protected $casts = [

        /*
        |--------------------------------------------------------------------------
        | DURUM
        |--------------------------------------------------------------------------
        */

        'maintenance_mode' => 'boolean',
        'maintenance_ends_at' => 'datetime',
        'auto_punishment_enabled' => 'boolean',
        'site_announcement_enabled' => 'boolean',
        'site_announcement_starts_at' => 'datetime',
        'site_announcement_ends_at' => 'datetime',
        'home_news_enabled' => 'boolean',
        'home_announcements_enabled' => 'boolean',
        'home_forum_enabled' => 'boolean',
        'home_galleries_enabled' => 'boolean',
        'home_videos_enabled' => 'boolean',
        'home_polls_enabled' => 'boolean',
        'home_breaking_news_enabled' => 'boolean',
        'home_announcement_bar_enabled' => 'boolean',
        'weather_enabled' => 'boolean',
        'weather_cache_minutes' => 'integer',

        /*
        |--------------------------------------------------------------------------
        | ÜYELİK
        |--------------------------------------------------------------------------
        */

        'registration_enabled' => 'boolean',
        'email_verification_required' => 'boolean',

        /*
        |--------------------------------------------------------------------------
        | TOPLULUK & CANLI SİSTEM
        |--------------------------------------------------------------------------
        */

        'forum_enabled' => 'boolean',

        'live_chat_enabled' => 'boolean',

        'live_stream_enabled' => 'boolean',

        'live_announcement_enabled' => 'boolean',

    ];

    public function hasActiveSiteAnnouncement(): bool
    {
        if (! $this->site_announcement_enabled || blank($this->site_announcement_text)) {
            return false;
        }

        if ($this->site_announcement_starts_at && $this->site_announcement_starts_at->isFuture()) {
            return false;
        }

        if ($this->site_announcement_ends_at && $this->site_announcement_ends_at->isPast()) {
            return false;
        }

        return true;
    }

    public function homeModuleEnabled(string $module): bool
    {
        $defaults = [
            'news' => true,
            'announcements' => true,
            'forum' => false,
            'galleries' => true,
            'videos' => true,
            'polls' => false,
            'breaking_news' => false,
            'announcement_bar' => false,
        ];

        $attribute = 'home_' . $module . '_enabled';

        return (bool) ($this->{$attribute} ?? $defaults[$module] ?? false);
    }

    public function homeModuleSignature(): string
    {
        return collect([
            'news',
            'announcements',
            'forum',
            'galleries',
            'videos',
            'polls',
            'breaking_news',
            'announcement_bar',
        ])
            ->map(fn (string $module): string => $module . ':' . (int) $this->homeModuleEnabled($module))
            ->implode('|');
    }
}
