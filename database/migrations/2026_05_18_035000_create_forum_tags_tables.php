<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('color', 20)->default('#ef4444');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('forum_tag_topic', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_tag_id')->constrained()->cascadeOnDelete();
            $table->foreignId('forum_topic_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['forum_tag_id', 'forum_topic_id']);
            $table->index(['forum_topic_id', 'forum_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_tag_topic');
        Schema::dropIfExists('forum_tags');
    }
};
