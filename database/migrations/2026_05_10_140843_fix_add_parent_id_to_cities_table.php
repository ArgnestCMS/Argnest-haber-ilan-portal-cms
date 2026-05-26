<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('cities', 'parent_id')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('cities', 'parent_id')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->dropColumn('parent_id');
            });
        }
    }
};