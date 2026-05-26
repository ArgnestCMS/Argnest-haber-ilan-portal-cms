<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forum_topics', function (Blueprint $table) {
            $table->boolean('is_solved')->default(false)->after('is_locked');
            $table->foreignId('last_post_user_id')
                ->nullable()
                ->after('last_post_at')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('forum_topics', function (Blueprint $table) {
            $table->dropConstrainedForeignId('last_post_user_id');
            $table->dropColumn('is_solved');
        });
    }
};
