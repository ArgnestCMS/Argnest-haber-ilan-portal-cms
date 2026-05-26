<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();

            // Genel SEO
            $table->string('site_title')->nullable();
            $table->text('site_description')->nullable();
            $table->string('site_keywords')->nullable();

            // Open Graph
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();

            // Twitter
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();

            // SEO
            $table->string('canonical_url')->nullable();
            $table->boolean('indexing')->default(true);

            // Robots
            $table->boolean('robots_index')->default(true);
            $table->boolean('robots_follow')->default(true);

            // Analytics
            $table->text('google_analytics')->nullable();
            $table->text('google_tag_manager')->nullable();

            // Structured Data
            $table->longText('json_ld')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
    }
};