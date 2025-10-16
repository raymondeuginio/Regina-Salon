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
        private readonly ?string $name = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = $this->name;

        if (! $name && isset($notifiable->name)) {
            $name = $notifiable->name;
        }

        $name ??= 'Pengguna';

        return (new MailMessage)
            ->subject('Kode OTP Verifikasi Email Anda')
            ->greeting('Halo '.$name.'!')
            ->line('Terima kasih telah membuat akun di Regina Salon.')
            ->line('Gunakan kode OTP berikut untuk memverifikasi alamat email Anda:')
            ->line('# '.$this->otp)
            ->line('Kode ini akan kedaluwarsa dalam '.$this->expiresInMinutes.' menit.')
            ->line('Jika Anda tidak meminta kode ini, abaikan email ini.');
    }

    public function otp(): string
    {
        return $this->otp;
    }
}
