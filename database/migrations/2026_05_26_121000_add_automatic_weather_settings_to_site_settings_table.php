<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            if (! Schema::hasColumn('site_settings', 'weather_enabled')) {
                $table->boolean('weather_enabled')->default(true)->after('weather_status_fallback');
            }

            if (! Schema::hasColumn('site_settings', 'weather_local_fallback_city')) {
                $table->string('weather_local_fallback_city')->nullable()->after('weather_enabled');
            }

            if (! Schema::hasColumn('site_settings', 'weather_cache_minutes')) {
                $table->unsignedSmallInteger('weather_cache_minutes')->default(10)->after('weather_local_fallback_city');
            }
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            if (Schema::hasColumn('site_settings', 'weather_cache_minutes')) {
                $table->dropColumn('weather_cache_minutes');
            }

            if (Schema::hasColumn('site_settings', 'weather_local_fallback_city')) {
                $table->dropColumn('weather_local_fallback_city');
            }

            if (Schema::hasColumn('site_settings', 'weather_enabled')) {
                $table->dropColumn('weather_enabled');
            }
        });
    }
};
