<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_category_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->enum('status', ['pending', 'published', 'hidden'])->default('published');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamp('last_post_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'is_pinned', 'last_post_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_topics');
    }
};
