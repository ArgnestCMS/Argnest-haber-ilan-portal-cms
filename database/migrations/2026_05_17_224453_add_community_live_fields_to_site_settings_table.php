<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('forum_enabled')->default(false)->after('id');

            $table->boolean('live_chat_enabled')->default(false)->after('forum_enabled');

            $table->boolean('live_stream_enabled')->default(false)->after('live_chat_enabled');
            $table->string('live_stream_title')->nullable()->after('live_stream_enabled');
            $table->text('live_stream_description')->nullable()->after('live_stream_title');
            $table->text('live_stream_url')->nullable()->after('live_stream_description');

            $table->boolean('live_announcement_enabled')->default(false)->after('live_stream_url');
            $table->string('live_announcement_text')->nullable()->after('live_announcement_enabled');
            $table->string('live_announcement_type')->default('info')->after('live_announcement_text');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'forum_enabled',
                'live_chat_enabled',
                'live_stream_enabled',
                'live_stream_title',
                'live_stream_description',
                'live_stream_url',
                'live_announcement_enabled',
                'live_announcement_text',
                'live_announcement_type',
            ]);
        });
    }
};