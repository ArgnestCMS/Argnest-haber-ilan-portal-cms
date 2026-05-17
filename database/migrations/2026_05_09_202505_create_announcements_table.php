<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {

            $table->id();

            $table->string('title');
            $table->string('slug')->unique();

            $table->text('summary')->nullable();
            $table->longText('content');

            $table->string('institution')->nullable();
            $table->string('city')->nullable();
            $table->string('category')->nullable();

            $table->dateTime('publish_date')->nullable();
            $table->dateTime('deadline')->nullable();

            $table->string('source')->nullable();

            $table->string('image')->nullable();
            $table->string('document')->nullable();

            $table->boolean('is_headline')->default(false);
            $table->boolean('comments_enabled')->default(true);
            $table->boolean('is_active')->default(true);

            $table->integer('views')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};