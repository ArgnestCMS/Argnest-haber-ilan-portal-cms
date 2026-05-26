<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forum_topics', function (Blueprint $table) {
            $table->boolean('replies_closed')->default(false)->after('is_solved');
            $table->unsignedSmallInteger('slow_mode_seconds')->default(0)->after('replies_closed');
            $table->text('moderator_note')->nullable()->after('slow_mode_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('forum_topics', function (Blueprint $table) {
            $table->dropColumn([
                'moderator_note',
                'slow_mode_seconds',
                'replies_closed',
            ]);
        });
    }
};
