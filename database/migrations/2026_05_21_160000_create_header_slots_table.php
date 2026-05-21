<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('header_slots', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slot_type')->default('button');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('display_position')->default('topbar_after_home');
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();

            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('button_target')->default('_self');
            $table->string('button_background_color')->nullable();
            $table->string('button_hover_color')->nullable();
            $table->string('button_text_color')->nullable();
            $table->string('button_size')->default('medium');
            $table->unsignedSmallInteger('button_radius')->default(6);
            $table->string('button_icon')->nullable();
            $table->string('custom_css_class')->nullable();

            $table->string('banner_image')->nullable();
            $table->string('banner_url')->nullable();
            $table->string('banner_target')->default('_self');
            $table->unsignedSmallInteger('banner_width')->nullable();
            $table->unsignedSmallInteger('banner_height')->nullable();
            $table->string('banner_alt')->nullable();
            $table->text('html_code')->nullable();
            $table->text('script_code')->nullable();

            $table->timestamps();

            $table->index(['is_active', 'display_position', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('header_slots');
    }
};
