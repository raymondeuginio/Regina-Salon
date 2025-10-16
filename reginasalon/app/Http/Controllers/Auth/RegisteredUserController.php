<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\EmailOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function __construct(private readonly EmailOtpService $emailOtpService)
    {
    }

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email', 'ends_with:gmail.com,yahoo.com'],
                'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ],
            [
                'email.ends_with' => __('Masukkan email yang valid.'),
            ]
        );

        Session::forget('pending_verification_user_id');
        Session::forget('pending_registration');

        $pendingUser = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],
        ];

        Session::put('pending_registration.user', $pendingUser);

        $this->emailOtpService->generateForPendingRegistration($pendingUser);

        return redirect()->route('verification.otp.show')->with('status', 'otp-sent');
    }
}
