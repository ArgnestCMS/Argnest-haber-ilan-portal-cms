<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->cascadeOnDelete();
            $table->foreignId('poll_option_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->string('ip_hash')->nullable();
            $table->string('voter_key');
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['poll_id', 'voter_key']);
            $table->unique(['poll_option_id', 'voter_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poll_votes');
    }
};
