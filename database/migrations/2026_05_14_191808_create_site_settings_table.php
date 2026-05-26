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
        Schema::create('site_settings', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | GENEL
            |--------------------------------------------------------------------------
            */

            $table->string('site_name')->nullable();
            $table->string('site_slogan')->nullable();

            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

            $table->string('seo_title')->nullable();

            $table->text('seo_description')->nullable();

            $table->text('seo_keywords')->nullable();

            /*
            |--------------------------------------------------------------------------
            | İLETİŞİM
            |--------------------------------------------------------------------------
            */

            $table->string('email')->nullable();

            $table->string('phone')->nullable();

            $table->string('address')->nullable();

            /*
            |--------------------------------------------------------------------------
            | SOSYAL MEDYA
            |--------------------------------------------------------------------------
            */

            $table->string('facebook')->nullable();

            $table->string('twitter')->nullable();

            $table->string('instagram')->nullable();

            $table->string('youtube')->nullable();

            $table->string('telegram')->nullable();

            /*
            |--------------------------------------------------------------------------
            | KOD ALANLARI
            |--------------------------------------------------------------------------
            */

            $table->longText('header_scripts')->nullable();

            $table->longText('footer_scripts')->nullable();

            $table->longText('google_analytics')->nullable();

            $table->longText('adsense_code')->nullable();

            /*
            |--------------------------------------------------------------------------
            | FOOTER
            |--------------------------------------------------------------------------
            */

            $table->text('footer_about')->nullable();

            $table->text('footer_copyright')->nullable();

            /*
            |--------------------------------------------------------------------------
            | DURUM
            |--------------------------------------------------------------------------
            */

            $table->boolean('maintenance_mode')
                ->default(false);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};