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

        if (! $user) {
            return redirect()->route('register');
        }

        return view('auth.verify-otp', [
            'email' => $user->email,
            'status' => session('status'),
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $user = $this->resolvePendingUser($request);

        if (! $user) {
            return redirect()->route('register');
        }

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

        $request->session()->forget('pending_verification_user_id');

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false))->with('status', 'email-verified');
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = $this->resolvePendingUser($request);

        if (! $user) {
            return redirect()->route('register');
        }

        $this->emailOtpService->resend($user);

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
}
