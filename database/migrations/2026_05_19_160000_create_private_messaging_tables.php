<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'message_privacy')) {
                $table->string('message_privacy', 20)->default('followers')->after('community_trust_score');
            }
        });

        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->default('direct')->index();
            $table->string('status', 20)->default('pending')->index();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('last_read_at')->nullable();
            $table->boolean('is_muted')->default(false);
            $table->timestamp('muted_until')->nullable();
            $table->timestamps();

            $table->unique(['conversation_id', 'user_id']);
            $table->index(['user_id', 'last_read_at']);
        });

        Schema::create('private_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->string('status', 20)->default('sent')->index();
            $table->unsignedTinyInteger('ai_risk_score')->default(0);
            $table->string('ai_risk_label', 20)->default('low')->index();
            $table->json('ai_risk_reasons')->nullable();
            $table->boolean('ai_review_required')->default(false)->index();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
            $table->index(['sender_id', 'created_at']);
        });

        Schema::create('user_message_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blocker_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('blocked_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('muted_until')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->unique(['blocker_id', 'blocked_id']);
            $table->index(['blocked_id', 'blocker_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_message_blocks');
        Schema::dropIfExists('private_messages');
        Schema::dropIfExists('conversation_participants');
        Schema::dropIfExists('conversations');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'message_privacy')) {
                $table->dropColumn('message_privacy');
            }
        });
    }
};
