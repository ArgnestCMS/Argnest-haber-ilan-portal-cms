<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            if (! Schema::hasColumn('site_settings', 'weather_city')) {
                $table->string('weather_city')->nullable()->after('home_announcement_bar_enabled');
            }

            if (! Schema::hasColumn('site_settings', 'weather_temperature_fallback')) {
                $table->string('weather_temperature_fallback', 20)->nullable()->after('weather_city');
            }

            if (! Schema::hasColumn('site_settings', 'weather_status_fallback')) {
                $table->string('weather_status_fallback')->nullable()->after('weather_temperature_fallback');
            }
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            if (Schema::hasColumn('site_settings', 'weather_status_fallback')) {
                $table->dropColumn('weather_status_fallback');
            }

            if (Schema::hasColumn('site_settings', 'weather_temperature_fallback')) {
                $table->dropColumn('weather_temperature_fallback');
            }

            if (Schema::hasColumn('site_settings', 'weather_city')) {
                $table->dropColumn('weather_city');
            }
        });
    }
};
