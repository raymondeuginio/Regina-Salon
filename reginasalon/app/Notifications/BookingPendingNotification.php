<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingPendingNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public array $data)
    {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $customerName = $this->data['customer_name'] ?? ($notifiable->name ?? 'Pelanggan Regina Salon');
        $bookingDate = $this->data['booking_date'] ?? '-';
        $bookingTime = $this->data['booking_time'] ?? '-';
        $storeName = $this->data['store']['name'] ?? 'Regina Salon';
        $storeAddress = $this->data['store']['address'] ?? null;
        $services = $this->data['services'] ?? [];
        $totalDuration = $this->data['total_duration'] ?? null;
        $notes = $this->data['notes'] ?? null;

        $mail = (new MailMessage())
            ->subject('Booking Pending - Regina Salon')
            ->greeting('Halo ' . $customerName . '!')
            ->line('Terima kasih telah melakukan booking di Regina Salon. Permintaan Anda sudah kami terima dan saat ini berstatus pending.')
            ->line('Berikut detail booking Anda:')
            ->line('Tanggal: ' . $bookingDate)
            ->line('Waktu: ' . $bookingTime . ' WIB')
            ->line('Cabang: ' . $storeName);

        if ($storeAddress) {
            $mail->line('Alamat: ' . $storeAddress);
        }

        if (! empty($services)) {
            $mail->line('Layanan:');

            foreach ($services as $service) {
                $serviceName = $service['name'] ?? '-';
                $duration = $service['duration'] ?? null;
                $staffName = $service['staff'] ?? null;

                $details = $serviceName;
                if ($duration) {
                    $details .= ' • ' . $duration . ' menit';
                }
                if ($staffName) {
                    $details .= ' • Stylist: ' . $staffName;
                }

                $mail->line('- ' . $details);
            }
        }

        if ($totalDuration) {
            $mail->line('Total Durasi: ' . $totalDuration . ' menit');
        }

        if (! empty($this->data['total_price'])) {
            $mail->line('Perkiraan Biaya: Rp' . number_format((float) $this->data['total_price'], 0, ',', '.'));
        }

        if ($notes) {
            $mail->line('Catatan Anda: ' . $notes);
        }

        return $mail
            ->line('Kami akan mengirimkan pengingat melalui email ketika booking Anda sudah dikonfirmasi.')
            ->line('Sampai jumpa di salon!');
    }
}
