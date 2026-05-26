<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forum_posts', function (Blueprint $table) {
            $table->foreignId('parent_id')
                ->nullable()
                ->after('user_id')
                ->constrained('forum_posts')
                ->nullOnDelete();

            $table->foreignId('quoted_post_id')
                ->nullable()
                ->after('parent_id')
                ->constrained('forum_posts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('forum_posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('quoted_post_id');
            $table->dropConstrainedForeignId('parent_id');
        });
    }
};
