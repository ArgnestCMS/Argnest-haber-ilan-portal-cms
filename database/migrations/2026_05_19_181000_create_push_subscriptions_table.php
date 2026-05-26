<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('endpoint');
            $table->string('endpoint_hash', 64)->unique();
            $table->string('public_key');
            $table->string('auth_token');
            $table->string('content_encoding', 20)->default('aes128gcm');
            $table->string('user_agent')->nullable();
            $table->boolean('is_enabled')->default(true)->index();
            $table->json('preferences')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->unsignedSmallInteger('failure_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'is_enabled']);
            $table->index(['failed_at', 'failure_count']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
    }
};
