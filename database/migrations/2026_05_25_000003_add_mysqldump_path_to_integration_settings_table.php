<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('integration_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('integration_settings', 'mysqldump_path')) {
                $table->string('mysqldump_path')->nullable()->after('captcha_required');
            }
        });
    }

    public function down(): void
    {
        Schema::table('integration_settings', function (Blueprint $table) {
            if (Schema::hasColumn('integration_settings', 'mysqldump_path')) {
                $table->dropColumn('mysqldump_path');
            }
        });
    }
};
