<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_seen_at')->nullable()->after('remember_token');
            $table->string('last_ip_address')->nullable()->after('last_seen_at');
            $table->string('last_device')->nullable()->after('last_ip_address');
            $table->string('last_browser')->nullable()->after('last_device');
            $table->string('last_platform')->nullable()->after('last_browser');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_seen_at',
                'last_ip_address',
                'last_device',
                'last_browser',
                'last_platform',
            ]);
        });
    }
};