<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('live_activities', function (Blueprint $table) {
            $table->boolean('is_important')->default(false)->after('is_public');
            $table->softDeletes();

            $table->index(['is_important', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::table('live_activities', function (Blueprint $table) {
            $table->dropIndex(['is_important', 'occurred_at']);
            $table->dropSoftDeletes();
            $table->dropColumn('is_important');
        });
    }
};
