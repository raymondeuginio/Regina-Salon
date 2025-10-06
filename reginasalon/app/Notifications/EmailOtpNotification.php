<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailOtpNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $otp,
        private readonly int $expiresInMinutes,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Kode OTP Verifikasi Email Anda')
            ->greeting('Halo '.$notifiable->name.'!')
            ->line('Terima kasih telah membuat akun di Regina Salon.')
            ->line('Gunakan kode OTP berikut untuk memverifikasi alamat email Anda:')
            ->line('# '.$this->otp)
            ->line('Kode ini akan kedaluwarsa dalam '.$this->expiresInMinutes.' menit.')
            ->line('Jika Anda tidak meminta kode ini, abaikan email ini.');
    }
}
