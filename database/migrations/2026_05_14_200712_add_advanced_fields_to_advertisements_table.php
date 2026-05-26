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
        Schema::table('advertisements', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | REKLAM TÜRÜ
            |--------------------------------------------------------------------------
            */

            $table->string('ad_type')
                ->default('image')
                ->after('position');

            /*
            |--------------------------------------------------------------------------
            | HTML / ADSENSE
            |--------------------------------------------------------------------------
            */

            $table->longText('html_code')
                ->nullable()
                ->after('image');

            /*
            |--------------------------------------------------------------------------
            | CİHAZ HEDEFLEME
            |--------------------------------------------------------------------------
            */

            $table->string('device_target')
                ->default('all')
                ->after('ad_type');

            /*
            |--------------------------------------------------------------------------
            | SAYFA HEDEFLEME
            |--------------------------------------------------------------------------
            */

            $table->string('page_target')
                ->default('all')
                ->after('device_target');

            /*
            |--------------------------------------------------------------------------
            | İSTATİSTİK
            |--------------------------------------------------------------------------
            */

            $table->decimal('ctr', 8, 2)
                ->default(0)
                ->after('clicks');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {

            $table->dropColumn([
                'ad_type',
                'html_code',
                'device_target',
                'page_target',
                'ctr',
            ]);

        });
    }
};