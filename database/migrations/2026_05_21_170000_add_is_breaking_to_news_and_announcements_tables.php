<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table): void {
            if (! Schema::hasColumn('news', 'is_breaking')) {
                $table->boolean('is_breaking')->default(false)->after('is_headline')->index();
            }
        });

        Schema::table('announcements', function (Blueprint $table): void {
            if (! Schema::hasColumn('announcements', 'is_breaking')) {
                $table->boolean('is_breaking')->default(false)->after('is_headline')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table): void {
            if (Schema::hasColumn('news', 'is_breaking')) {
                $table->dropColumn('is_breaking');
            }
        });

        Schema::table('announcements', function (Blueprint $table): void {
            if (Schema::hasColumn('announcements', 'is_breaking')) {
                $table->dropColumn('is_breaking');
            }
        });
    }
};
