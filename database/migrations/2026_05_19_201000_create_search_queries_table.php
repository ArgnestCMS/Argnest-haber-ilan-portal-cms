<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('search_queries', function (Blueprint $table) {
            $table->id();
            $table->string('query', 120);
            $table->string('normalized_query', 120)->unique();
            $table->unsignedInteger('hits')->default(1);
            $table->timestamp('last_searched_at')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['hits', 'last_searched_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_queries');
    }
};
