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
        Schema::table('news', function (Blueprint $table) {

            $table->unsignedBigInteger('daily_views')
                ->default(0)
                ->after('views');

            $table->unsignedBigInteger('weekly_views')
                ->default(0)
                ->after('daily_views');

            $table->unsignedBigInteger('monthly_views')
                ->default(0)
                ->after('weekly_views');

            $table->unsignedBigInteger('trend_score')
                ->default(0)
                ->after('monthly_views');

            $table->timestamp('last_viewed_at')
                ->nullable()
                ->after('trend_score');

            $table->boolean('is_trending')
                ->default(false)
                ->after('last_viewed_at');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {

            $table->dropColumn([

                'daily_views',
                'weekly_views',
                'monthly_views',
                'trend_score',
                'last_viewed_at',
                'is_trending',

            ]);

        });
    }
};