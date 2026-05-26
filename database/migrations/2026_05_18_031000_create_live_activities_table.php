<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->nullableMorphs('subject');
            $table->string('type', 80);
            $table->string('source', 40)->default('system');
            $table->string('severity', 20)->default('info');
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('url')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();

            $table->index(['is_public', 'occurred_at']);
            $table->index(['source', 'type']);
            $table->index(['severity', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_activities');
    }
};
