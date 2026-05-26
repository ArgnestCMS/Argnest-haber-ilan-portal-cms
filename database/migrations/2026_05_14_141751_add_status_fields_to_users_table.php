<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('status')
                ->default('active')
                ->after('role');

            // active
            // suspended
            // banned
            // frozen

            $table->timestamp('suspended_until')
                ->nullable()
                ->after('status');

            $table->text('ban_reason')
                ->nullable()
                ->after('suspended_until');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'status',
                'suspended_until',
                'ban_reason',
            ]);

        });
    }
};