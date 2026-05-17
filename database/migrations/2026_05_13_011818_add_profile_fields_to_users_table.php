<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('avatar')->nullable();

            $table->text('bio')->nullable();

            $table->string('facebook')->nullable();

            $table->string('twitter')->nullable();

            $table->string('instagram')->nullable();

            $table->string('youtube')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'avatar',
                'bio',
                'facebook',
                'twitter',
                'instagram',
                'youtube',
            ]);

        });
    }
};