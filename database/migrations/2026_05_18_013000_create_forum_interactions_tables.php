<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('forum_reputation')->default(0)->after('bio');
        });

        Schema::create('forum_topic_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_topic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['forum_topic_id', 'user_id']);
        });

        Schema::create('forum_topic_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_topic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['forum_topic_id', 'user_id']);
        });

        Schema::create('forum_badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->string('color')->default('slate');
            $table->timestamps();
        });

        Schema::create('forum_badge_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_badge_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['forum_badge_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_badge_user');
        Schema::dropIfExists('forum_badges');
        Schema::dropIfExists('forum_topic_bookmarks');
        Schema::dropIfExists('forum_topic_likes');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('forum_reputation');
        });
    }
};
