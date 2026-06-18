<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // GET /reset-password/{token}
    // -------------------------------------------------------------------------

    public function test_reset_password_form_is_displayed(): void
    {
        $response = $this->get('/reset-password/some-token?email=hiker@example.com');

        $response->assertStatus(200);
        $response->assertViewIs('auth.reset-password');
        $response->assertViewHas('token', 'some-token');
        $response->assertViewHas('email', 'hiker@example.com');
    }

    // -------------------------------------------------------------------------
    // POST /reset-password — success
    // -------------------------------------------------------------------------

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password_hash));
    }

    public function test_token_is_invalidated_after_reset(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /reset-password — failures
    // -------------------------------------------------------------------------

    public function test_password_is_not_reset_with_invalid_token(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/reset-password', [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertSessionHasErrors('email');
        // Password lama (dari factory) tetap berlaku.
        $this->assertTrue(Hash::check('password', $user->fresh()->password_hash));
    }

    public function test_reset_requires_matching_password_confirmation(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'different-456',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertTrue(Hash::check('password', $user->fresh()->password_hash));
    }

    public function test_reset_requires_minimum_password_length(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertTrue(Hash::check('password', $user->fresh()->password_hash));
    }

    public function test_reset_requires_token_and_email(): void
    {
        $response = $this->post('/reset-password', [
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertSessionHasErrors(['token', 'email']);
    }
}
