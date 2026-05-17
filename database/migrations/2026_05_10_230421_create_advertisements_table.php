<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {

            $table->id();

            $table->string('title');

            $table->string('position');
            // top_banner
            // bottom_banner
            // left_sidebar
            // right_sidebar

            $table->string('image')->nullable();

            $table->string('url')->nullable();

            $table->boolean('is_active')->default(true);

            $table->integer('views')->default(0);

            $table->integer('clicks')->default(0);

            $table->timestamp('start_date')->nullable();

            $table->timestamp('end_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};