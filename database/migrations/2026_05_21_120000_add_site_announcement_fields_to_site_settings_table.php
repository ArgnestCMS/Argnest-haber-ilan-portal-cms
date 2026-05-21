<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('site_announcement_enabled')->default(false);
            $table->string('site_announcement_icon')->nullable();
            $table->string('site_announcement_text')->nullable();
            $table->dateTime('site_announcement_starts_at')->nullable();
            $table->dateTime('site_announcement_ends_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'site_announcement_enabled',
                'site_announcement_icon',
                'site_announcement_text',
                'site_announcement_starts_at',
                'site_announcement_ends_at',
            ]);
        });
    }
};
