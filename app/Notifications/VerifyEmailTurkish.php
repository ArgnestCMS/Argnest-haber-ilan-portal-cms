<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailTurkish extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('E-posta Adresinizi Doğrulayın')
            ->greeting('Merhaba ' . $notifiable->name . ' 👋')
            ->line('İlanHaber.net hesabınızı aktifleştirmek için e-posta adresinizi doğrulamanız gerekiyor.')
            ->line('Aşağıdaki butona tıklayarak hesabınızı doğrulayabilirsiniz.')
            ->action('E-posta Adresimi Doğrula', $verificationUrl)
            ->line('Bu hesabı siz oluşturmadıysanız herhangi bir işlem yapmanıza gerek yoktur.')
            ->salutation('İlanHaber.net');
    }
}