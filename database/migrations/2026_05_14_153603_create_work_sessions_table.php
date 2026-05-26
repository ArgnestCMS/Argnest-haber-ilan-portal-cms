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
        Schema::create('work_sessions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('type');

            // work
            // break
            // lunch

            $table->string('status')
                ->default('active');

            // active
            // completed

            $table->timestamp('started_at');

            $table->timestamp('ended_at')
                ->nullable();

            $table->integer('duration_minutes')
                ->default(0);

            $table->string('ip_address')
                ->nullable();

            $table->string('device')
                ->nullable();

            $table->string('browser')
                ->nullable();

            $table->string('platform')
                ->nullable();

            $table->text('note')
                ->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_sessions');
    }
};