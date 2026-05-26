<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('topic')->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->boolean('share_results')->default(true);
            $table->boolean('show_home_popup')->default(false);
            $table->unsignedInteger('popup_cooldown_minutes')->default(1440);
            $table->boolean('allow_multiple')->default(false);
            $table->boolean('allow_guests')->default(true);
            $table->boolean('require_login')->default(false);
            $table->string('duplicate_guard')->default('user_session_ip');
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
};
