<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('seo_settings', 'default_author')) {
                $table->string('default_author')->nullable()->after('site_keywords');
            }

            if (! Schema::hasColumn('seo_settings', 'default_language')) {
                $table->string('default_language', 10)->default('tr')->after('default_author');
            }

            if (! Schema::hasColumn('seo_settings', 'robots_txt')) {
                $table->longText('robots_txt')->nullable()->after('robots_follow');
            }

            if (! Schema::hasColumn('seo_settings', 'sitemap_cache_minutes')) {
                $table->unsignedInteger('sitemap_cache_minutes')->default(60)->after('robots_txt');
            }
        });
    }

    public function down(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            foreach (['default_author', 'default_language', 'robots_txt', 'sitemap_cache_minutes'] as $column) {
                if (Schema::hasColumn('seo_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
