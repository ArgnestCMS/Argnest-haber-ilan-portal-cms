<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            if (! Schema::hasColumn('site_settings', 'maintenance_message')) {
                $table->text('maintenance_message')->nullable()->after('maintenance_mode');
            }

            if (! Schema::hasColumn('site_settings', 'maintenance_ends_at')) {
                $table->dateTime('maintenance_ends_at')->nullable()->after('maintenance_message');
            }
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            if (Schema::hasColumn('site_settings', 'maintenance_ends_at')) {
                $table->dropColumn('maintenance_ends_at');
            }

            if (Schema::hasColumn('site_settings', 'maintenance_message')) {
                $table->dropColumn('maintenance_message');
            }
        });
    }
};
