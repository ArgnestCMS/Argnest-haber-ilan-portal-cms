<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('home_news_enabled')->default(true)->after('auto_punishment_enabled');
            $table->boolean('home_announcements_enabled')->default(true)->after('home_news_enabled');
            $table->boolean('home_forum_enabled')->default(false)->after('home_announcements_enabled');
            $table->boolean('home_galleries_enabled')->default(true)->after('home_forum_enabled');
            $table->boolean('home_videos_enabled')->default(true)->after('home_galleries_enabled');
            $table->boolean('home_polls_enabled')->default(false)->after('home_videos_enabled');
            $table->boolean('home_breaking_news_enabled')->default(false)->after('home_polls_enabled');
            $table->boolean('home_announcement_bar_enabled')->default(false)->after('home_breaking_news_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'home_news_enabled',
                'home_announcements_enabled',
                'home_forum_enabled',
                'home_galleries_enabled',
                'home_videos_enabled',
                'home_polls_enabled',
                'home_breaking_news_enabled',
                'home_announcement_bar_enabled',
            ]);
        });
    }
};
