<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // GET /register
    // -------------------------------------------------------------------------

    public function test_registration_form_is_displayed(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    // -------------------------------------------------------------------------
    // POST /register — success
    // -------------------------------------------------------------------------

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->post(route('register.submit'), [
            'username'              => 'newuser',
            'email'                 => 'newuser@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /register — validation failures
    // -------------------------------------------------------------------------

    public function test_registration_requires_username(): void
    {
        $response = $this->post(route('register.submit'), [
            'email'                 => 'newuser@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('username');
    }

    public function test_registration_requires_email(): void
    {
        $response = $this->post(route('register.submit'), [
            'username'              => 'newuser',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_registration_requires_password(): void
    {
        $response = $this->post(route('register.submit'), [
            'username' => 'newuser',
            'email'    => 'newuser@example.com',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $response = $this->post(route('register.submit'), [
            'username'              => 'newuser',
            'email'                 => 'newuser@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_registration_requires_minimum_password_length(): void
    {
        $response = $this->post(route('register.submit'), [
            'username'              => 'newuser',
            'email'                 => 'newuser@example.com',
            'password'              => 'short1',        // 6 chars — under 8
            'password_confirmation' => 'short1',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_registration_requires_unique_email(): void
    {
        $existing = User::factory()->create([
            'email' => 'taken@example.com',
        ]);

        $response = $this->post(route('register.submit'), [
            'username'              => 'brandnewuser',
            'email'                 => 'taken@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_registration_requires_unique_username(): void
    {
        $existing = User::factory()->create([
            'username' => 'takenusername',
        ]);

        $response = $this->post(route('register.submit'), [
            'username'              => 'takenusername',
            'email'                 => 'unique@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('username');
    }
}
