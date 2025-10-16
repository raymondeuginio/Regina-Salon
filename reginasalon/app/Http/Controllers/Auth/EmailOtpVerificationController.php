<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailOtp;
use App\Models\User;
use App\Services\EmailOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EmailOtpVerificationController extends Controller
{
    public function __construct(private readonly EmailOtpService $emailOtpService)
    {
    }

    public function show(Request $request): View|RedirectResponse
    {
        $user = $this->resolvePendingUser($request);

        if ($user) {
            $email = $user->email;
        } else {
            $pendingRegistration = $this->resolvePendingRegistration($request);

            if (! $pendingRegistration) {
                return redirect()->route('register');
            }

            $email = $pendingRegistration['email'];
        }

        return view('auth.verify-otp', [
            'email' => $email,
            'status' => session('status'),
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $user = $this->resolvePendingUser($request);

        if ($user) {
            if ($user->hasVerifiedEmail()) {
                Auth::login($user);

                return redirect()->intended(route('dashboard', absolute: false));
            }

            $otp = EmailOtp::where('user_id', $user->id)
                ->where('type', 'registration')
                ->latest()
                ->first();

            if (! $otp || $otp->hasBeenVerified()) {
                throw ValidationException::withMessages([
                    'otp' => __('Kode OTP tidak ditemukan. Silakan minta kode baru.'),
                ]);
            }

            if ($otp->isExpired()) {
                throw ValidationException::withMessages([
                    'otp' => __('Kode OTP sudah kedaluwarsa. Silakan kirim ulang kode.'),
                ]);
            }

            if (! Hash::check($request->string('otp'), $otp->code)) {
                throw ValidationException::withMessages([
                    'otp' => __('Kode OTP yang Anda masukkan tidak valid.'),
                ]);
            }

            $otp->markAsVerified();

            $user->forceFill([
                'email_verified_at' => now(),
            ])->save();

            $request->session()->forget(['pending_verification_user_id', 'pending_registration']);

            Auth::login($user);

            return redirect()->intended(route('dashboard', absolute: false))->with('status', 'email-verified');
        }

        $pendingRegistration = $this->resolvePendingRegistration($request);

        if (! $pendingRegistration) {
            return redirect()->route('register');
        }

        $otp = $request->session()->get('pending_registration.otp');

        if (! $otp || ! isset($otp['code'])) {
            throw ValidationException::withMessages([
                'otp' => __('Kode OTP tidak ditemukan. Silakan minta kode baru.'),
            ]);
        }

        $expiresAt = isset($otp['expires_at']) ? Carbon::parse($otp['expires_at']) : null;

        if (! $expiresAt || $expiresAt->isPast()) {
            throw ValidationException::withMessages([
                'otp' => __('Kode OTP sudah kedaluwarsa. Silakan kirim ulang kode.'),
            ]);
        }

        if (! Hash::check($request->string('otp'), $otp['code'])) {
            throw ValidationException::withMessages([
                'otp' => __('Kode OTP yang Anda masukkan tidak valid.'),
            ]);
        }

        $user = User::create([
            'name' => $pendingRegistration['name'],
            'email' => $pendingRegistration['email'],
            'phone' => $pendingRegistration['phone'],
            'password' => $pendingRegistration['password'],
        ]);

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        $request->session()->forget(['pending_registration', 'pending_verification_user_id']);

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false))->with('status', 'email-verified');
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = $this->resolvePendingUser($request);

        if ($user) {
            $this->emailOtpService->resend($user);

            return back()->with('status', 'otp-resent');
        }

        $pendingRegistration = $this->resolvePendingRegistration($request);

        if (! $pendingRegistration) {
            return redirect()->route('register');
        }

        $this->emailOtpService->generateForPendingRegistration($pendingRegistration);

        return back()->with('status', 'otp-resent');
    }

    private function resolvePendingUser(Request $request): ?User
    {
        $userId = $request->session()->get('pending_verification_user_id');

        if (! $userId) {
            return null;
        }

        return User::find($userId);
    }

    private function resolvePendingRegistration(Request $request): ?array
    {
        $pendingRegistration = $request->session()->get('pending_registration.user');

        if (! is_array($pendingRegistration)) {
            return null;
        }

        return $pendingRegistration;
    }
}
