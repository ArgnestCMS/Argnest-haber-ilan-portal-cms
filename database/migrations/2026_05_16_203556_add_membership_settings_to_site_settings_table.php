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
        Schema::table('site_settings', function (Blueprint $table) {

            $table->boolean('registration_enabled')
                ->default(true);

            $table->boolean('email_verification_required')
                ->default(true);

            $table->longText('membership_agreement')
                ->nullable();

            $table->longText('privacy_policy')
                ->nullable();

            $table->longText('community_rules')
                ->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {

            $table->dropColumn([
                'registration_enabled',
                'email_verification_required',
                'membership_agreement',
                'privacy_policy',
                'community_rules',
            ]);

        });
    }
};