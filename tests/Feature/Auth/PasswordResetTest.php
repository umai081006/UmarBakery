<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\CustomResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertSessionHas('status', 'Jika email tersebut terdaftar, kami telah mengirimkan instruksi reset password.');

        Notification::assertSentTo($user, CustomResetPasswordNotification::class);
    }

    public function test_non_existent_email_returns_same_generic_response(): void
    {
        Notification::fake();

        $response = $this->post('/forgot-password', [
            'email' => 'doesnotexist@example.com',
        ]);

        $response->assertSessionHas('status', 'Jika email tersebut terdaftar, kami telah mengirimkan instruksi reset password.');
        
        Notification::assertNothingSent();
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/reset-password/fake-token?email=test@example.com');

        $response->assertStatus(200);
    }

    public function test_password_can_be_reset_with_valid_token_and_auto_login(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        // After successful reset, it should auto-login and redirect to dashboard
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('status', 'Password berhasil diubah. Anda telah otomatis login.');
        
        // Ensure user is authenticated
        $this->assertAuthenticatedAs($user);
        
        // Ensure password is changed
        $this->assertTrue(Hash::check('new-password123', $user->fresh()->password));
        $this->assertFalse(Hash::check('old-password', $user->fresh()->password));
    }

    public function test_password_is_not_reset_with_invalid_token(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->post('/reset-password', [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        // Should return back with error on email field (due to invalid token)
        $response->assertSessionHasErrors('email');

        // User should not be authenticated
        $this->assertGuest();

        // Password should remain unchanged
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }
}
