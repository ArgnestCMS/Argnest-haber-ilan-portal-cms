<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->nullableMorphs('attachable');
            $table->string('collection', 40)->default('forum')->index();
            $table->string('disk', 40)->default('public');
            $table->string('visibility', 20)->default('public')->index();
            $table->string('original_name');
            $table->string('file_name');
            $table->string('path');
            $table->string('thumbnail_path')->nullable();
            $table->string('mime_type', 120);
            $table->string('extension', 20);
            $table->unsignedBigInteger('size');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('checksum', 64)->index();
            $table->string('status', 20)->default('ready')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'collection', 'created_at']);
            $table->index(['attachable_type', 'attachable_id', 'collection']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_assets');
    }
};
