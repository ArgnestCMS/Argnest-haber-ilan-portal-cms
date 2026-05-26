<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('forum_xp')->default(0)->after('forum_reputation');
            $table->unsignedSmallInteger('forum_level')->default(1)->after('forum_xp');
            $table->unsignedSmallInteger('forum_streak_days')->default(0)->after('forum_level');
            $table->date('forum_last_activity_date')->nullable()->after('forum_streak_days');
        });

        Schema::table('forum_badges', function (Blueprint $table) {
            $table->string('type')->default('reputation')->after('color');
            $table->unsignedInteger('threshold')->default(0)->after('type');
            $table->integer('xp_reward')->default(0)->after('threshold');
        });

        Schema::create('forum_reputation_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->integer('points')->default(0);
            $table->integer('xp')->default(0);
            $table->nullableMorphs('subject');
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['type', 'created_at']);
        });

        Schema::create('forum_quests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type');
            $table->unsignedInteger('target')->default(1);
            $table->integer('xp_reward')->default(0);
            $table->integer('reputation_reward')->default(0);
            $table->boolean('is_daily')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('forum_quest_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_quest_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('tracked_on');
            $table->unsignedInteger('progress')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['forum_quest_id', 'user_id', 'tracked_on']);
            $table->index(['user_id', 'tracked_on', 'is_completed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_quest_user');
        Schema::dropIfExists('forum_quests');
        Schema::dropIfExists('forum_reputation_events');

        Schema::table('forum_badges', function (Blueprint $table) {
            $table->dropColumn(['type', 'threshold', 'xp_reward']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'forum_xp',
                'forum_level',
                'forum_streak_days',
                'forum_last_activity_date',
            ]);
        });
    }
};
