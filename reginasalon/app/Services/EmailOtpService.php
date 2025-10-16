<?php

namespace App\Services;

use App\Models\EmailOtp;
use App\Models\User;
use App\Notifications\EmailOtpNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;

class EmailOtpService
{
    public function __construct(
        private readonly int $otpLength = 6,
        private readonly int $expiryMinutes = 10,
    ) {
    }

    public function generate(User $user, string $type = 'registration'): EmailOtp
    {
        EmailOtp::where('user_id', $user->id)
            ->where('type', $type)
            ->whereNull('verified_at')
            ->delete();

        $plainOtp = $this->generateOtpCode();

        $otp = EmailOtp::create([
            'user_id' => $user->id,
            'type' => $type,
            'code' => Hash::make($plainOtp),
            'expires_at' => now()->addMinutes($this->expiryMinutes),
        ]);

        Notification::send($user, new EmailOtpNotification($plainOtp, $this->expiryMinutes));

        return $otp;
    }

    public function resend(User $user, string $type = 'registration'): EmailOtp
    {
        return $this->generate($user, $type);
    }

    public function generateForPendingRegistration(array $pendingUser): void
    {
        $plainOtp = $this->generateOtpCode();

        Session::put('pending_registration.otp', [
            'code' => Hash::make($plainOtp),
            'expires_at' => now()->addMinutes($this->expiryMinutes),
        ]);

        Notification::route('mail', $pendingUser['email'])
            ->notify(new EmailOtpNotification($plainOtp, $this->expiryMinutes, $pendingUser['name'] ?? null));
    }

    private function generateOtpCode(): string
    {
        $min = (int) str_pad('1', $this->otpLength, '0');
        $max = (int) str_pad('', $this->otpLength, '9');

        return (string) random_int($min, $max);
    }
}
