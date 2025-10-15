<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingReminderMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $customerName,
        public ?string $storeName,
        public string $storeAddress,
        public string $dateLabel,
        public string $timeLabel,
        public array $services
    ) {
    }

    public function build(): self
    {
        return $this->subject('Pengingat Booking Regina Salon')
            ->view('emails.booking.reminder')
            ->with([
                'customerName' => $this->customerName,
                'storeName' => $this->storeName,
                'storeAddress' => $this->storeAddress,
                'dateLabel' => $this->dateLabel,
                'timeLabel' => $this->timeLabel,
                'services' => $this->services,
            ]);
    }
}
