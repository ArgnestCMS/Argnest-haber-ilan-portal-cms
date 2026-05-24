<?php

namespace App\Notifications;

use App\Models\SiteSetting;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailTurkish extends VerifyEmail
{
    use Queueable;

    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $siteName = SiteSetting::first()?->site_name ?? config('app.name');

        return (new MailMessage)
            ->subject('E-posta Adresinizi Doğrulayın')
            ->greeting('Merhaba ' . $notifiable->name . ' 👋')
            ->line($siteName . ' hesabınızı aktifleştirmek için e-posta adresinizi doğrulamanız gerekiyor.')
            ->line('Aşağıdaki butona tıklayarak hesabınızı doğrulayabilirsiniz.')
            ->action('E-posta Adresimi Doğrula', $verificationUrl)
            ->line('Bu hesabı siz oluşturmadıysanız herhangi bir işlem yapmanıza gerek yoktur.')
            ->salutation($siteName);
    }
}
