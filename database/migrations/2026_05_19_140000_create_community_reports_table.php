<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('community_trust_score')->default(50)->after('forum_reputation');
        });

        Schema::create('community_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('reportable');
            $table->string('reason', 40);
            $table->text('details')->nullable();
            $table->string('status', 20)->default('pending');
            $table->unsignedTinyInteger('subject_ai_risk_score')->default(0);
            $table->string('subject_ai_risk_label', 20)->default('low');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('moderator_note')->nullable();
            $table->string('resolution_action', 40)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'reportable_type', 'reportable_id'], 'community_reports_unique_user_subject');
            $table->index(['status', 'subject_ai_risk_score']);
            $table->index(['reason', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_reports');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('community_trust_score');
        });
    }
};
