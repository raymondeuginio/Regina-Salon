<?php

namespace Tests\Feature\Auth;

use App\Notifications\EmailOtpNotification;
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
        $this->assertDatabaseHas('users', [
            'email' => 'test@gmail.com',
            'phone' => '081234567890',
        ]);
    }
}
