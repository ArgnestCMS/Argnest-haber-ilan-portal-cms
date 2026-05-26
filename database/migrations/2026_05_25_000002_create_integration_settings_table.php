<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_settings', function (Blueprint $table) {
            $table->id();
            $table->string('mail_mailer')->nullable();
            $table->string('mail_host')->nullable();
            $table->unsignedInteger('mail_port')->nullable();
            $table->string('mail_username')->nullable();
            $table->text('mail_password')->nullable();
            $table->string('mail_encryption')->nullable();
            $table->string('mail_from_address')->nullable();
            $table->string('mail_from_name')->nullable();
            $table->boolean('recaptcha_enabled')->default(true);
            $table->string('recaptcha_site_key')->nullable();
            $table->text('recaptcha_secret_key')->nullable();
            $table->boolean('webpush_enabled')->default(false);
            $table->text('webpush_vapid_public_key')->nullable();
            $table->text('webpush_vapid_private_key')->nullable();
            $table->string('webpush_vapid_subject')->nullable();
            $table->string('google_client_id')->nullable();
            $table->text('google_client_secret')->nullable();
            $table->string('facebook_app_id')->nullable();
            $table->text('facebook_app_secret')->nullable();
            $table->boolean('captcha_required')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_settings');
    }
};
