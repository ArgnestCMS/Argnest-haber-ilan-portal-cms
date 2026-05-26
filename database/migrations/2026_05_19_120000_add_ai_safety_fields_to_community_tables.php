<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['forum_topics', 'forum_posts', 'live_chat_messages', 'comments'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedTinyInteger('ai_risk_score')->default(0)->after('status');
                $table->string('ai_risk_label', 20)->default('low')->after('ai_risk_score');
                $table->json('ai_risk_reasons')->nullable()->after('ai_risk_label');
                $table->boolean('ai_review_required')->default(false)->after('ai_risk_reasons');
                $table->index(['ai_review_required', 'ai_risk_score']);
            });
        }
    }

    public function down(): void
    {
        foreach (['forum_topics', 'forum_posts', 'live_chat_messages', 'comments'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropIndex(['ai_review_required', 'ai_risk_score']);
                $table->dropColumn([
                    'ai_risk_score',
                    'ai_risk_label',
                    'ai_risk_reasons',
                    'ai_review_required',
                ]);
            });
        }
    }
};
