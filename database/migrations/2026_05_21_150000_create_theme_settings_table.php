<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theme_settings', function (Blueprint $table) {
            $table->id();
            $table->string('primary_color', 20)->nullable()->default('#0878c9');
            $table->string('secondary_color', 20)->nullable()->default('#1e293b');
            $table->string('topbar_color', 20)->nullable()->default('#0878c9');
            $table->string('navbar_color', 20)->nullable()->default('#1e293b');
            $table->string('breaking_bar_color', 20)->nullable()->default('#dc2626');
            $table->string('announcement_bar_color', 20)->nullable()->default('#0f172a');
            $table->string('button_color', 20)->nullable()->default('#1d4ed8');
            $table->string('button_hover_color', 20)->nullable()->default('#1e40af');
            $table->string('link_color', 20)->nullable()->default('#1d4ed8');
            $table->string('heading_color', 20)->nullable()->default('#020617');
            $table->string('text_color', 20)->nullable()->default('#0f172a');
            $table->string('card_background_color', 20)->nullable()->default('#ffffff');
            $table->string('footer_color', 20)->nullable()->default('#0f172a');
            $table->timestamps();
        });

        DB::table('theme_settings')->insert([
            'primary_color' => '#0878c9',
            'secondary_color' => '#1e293b',
            'topbar_color' => '#0878c9',
            'navbar_color' => '#1e293b',
            'breaking_bar_color' => '#dc2626',
            'announcement_bar_color' => '#0f172a',
            'button_color' => '#1d4ed8',
            'button_hover_color' => '#1e40af',
            'link_color' => '#1d4ed8',
            'heading_color' => '#020617',
            'text_color' => '#0f172a',
            'card_background_color' => '#ffffff',
            'footer_color' => '#0f172a',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('theme_settings');
    }
};
