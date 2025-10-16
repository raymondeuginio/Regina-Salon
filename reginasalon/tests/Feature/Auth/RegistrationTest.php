<?php

namespace Tests\Feature\Auth;

use App\Notifications\EmailOtpNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'phone' => '081234567890',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('verification.otp.show'));

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'test@gmail.com']);

        $otp = null;

        Notification::assertSentOnDemand(EmailOtpNotification::class, function (EmailOtpNotification $notification, array $channels, object $notifiable) use (&$otp) {
            $otp = $notification->otp();

            return $notifiable->routeNotificationFor('mail') === 'test@gmail.com';
        });

        $this->assertNotNull(session('pending_registration.user'));
        $this->assertNotNull($otp);

        $verifyResponse = $this->post(route('verification.otp.verify'), [
            'otp' => $otp,
        ]);

        $verifyResponse->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
        $user = \App\Models\User::where('email', 'test@gmail.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('081234567890', $user->phone);
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_registration_rejects_non_supported_email_domains(): void
    {
        Notification::fake();

        $response = $this->from('/register')->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '081234567891',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
        $this->assertNull(session('pending_registration.user'));

        Notification::assertNothingSent();
    }

    public function test_registration_rejects_malformed_supported_email_addresses(): void
    {
        Notification::fake();

        $response = $this->from('/register')->post('/register', [
            'name' => 'Test User',
            'email' => 'asdadsaas@gmai',
            'phone' => '081234567893',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'asdadsaas@gmai']);
        $this->assertNull(session('pending_registration.user'));

        Notification::assertNothingSent();
    }

    public function test_user_is_not_created_when_otp_was_not_generated(): void
    {
        $response = $this->withSession([
            'pending_registration.user' => [
                'name' => 'Test User',
                'email' => 'test@gmail.com',
                'phone' => '081234567892',
                'password' => 'password',
            ],
        ])->post(route('verification.otp.verify'), [
            'otp' => '123456',
        ]);

        $response->assertSessionHasErrors('otp');
        $this->assertDatabaseMissing('users', ['email' => 'test@gmail.com']);
    }
}
