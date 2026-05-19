<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversation_participants', function (Blueprint $table) {
            if (! Schema::hasColumn('conversation_participants', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false)->after('muted_until');
                $table->timestamp('pinned_at')->nullable()->after('is_pinned');
            }
        });

        Schema::table('private_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('private_messages', 'edited_at')) {
                $table->timestamp('edited_at')->nullable()->after('ai_review_required');
            }

            if (! Schema::hasColumn('private_messages', 'deleted_at')) {
                $table->softDeletes()->after('edited_at');
            }
        });

        Schema::create('private_message_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('private_message_id')->constrained('private_messages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reaction', 20)->default('like');
            $table->timestamps();

            $table->unique(['private_message_id', 'user_id', 'reaction'], 'pm_reactions_unique');
            $table->index(['private_message_id', 'reaction']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('private_message_reactions');

        Schema::table('private_messages', function (Blueprint $table) {
            if (Schema::hasColumn('private_messages', 'deleted_at')) {
                $table->dropSoftDeletes();
            }

            if (Schema::hasColumn('private_messages', 'edited_at')) {
                $table->dropColumn('edited_at');
            }
        });

        Schema::table('conversation_participants', function (Blueprint $table) {
            if (Schema::hasColumn('conversation_participants', 'is_pinned')) {
                $table->dropColumn(['is_pinned', 'pinned_at']);
            }
        });
    }
};
