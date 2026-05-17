<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->string('source')->nullable()->after('content');
            $table->string('category')->nullable()->after('source');
            $table->dateTime('publish_date')->nullable()->after('category');
            $table->dateTime('end_date')->nullable()->after('publish_date');
            $table->string('news_type')->default('normal')->after('end_date');
            $table->boolean('share_facebook')->default(false)->after('news_type');
            $table->boolean('share_twitter')->default(false)->after('share_facebook');
            $table->string('document')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn([
                'source',
                'category',
                'publish_date',
                'end_date',
                'news_type',
                'share_facebook',
                'share_twitter',
                'document',
            ]);
        });
    }
};