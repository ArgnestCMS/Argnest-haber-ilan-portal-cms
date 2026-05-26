<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_announcements', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->string('icon')->nullable();
            $table->string('link_url')->nullable();
            $table->string('link_target')->default('_self');
            $table->boolean('is_active')->default(true);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $legacy = DB::table('site_settings')->first();

        if (
            $legacy
            && ($legacy->site_announcement_enabled ?? false)
            && filled($legacy->site_announcement_text ?? null)
        ) {
            DB::table('site_announcements')->insert([
                'text' => $legacy->site_announcement_text,
                'icon' => $legacy->site_announcement_icon ?: '📢',
                'is_active' => true,
                'starts_at' => $legacy->site_announcement_starts_at,
                'ends_at' => $legacy->site_announcement_ends_at,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_announcements');
    }
};
