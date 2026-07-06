<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Privilege escalation — role must not be mass assignable
    // -------------------------------------------------------------------------

    public function test_role_cannot_be_mass_assigned(): void
    {
        $user = User::create([
            'username' => 'attacker',
            'email' => 'attacker@example.com',
            'password_hash' => 'secret-password',
            'role' => 'admin', // attempted privilege escalation
        ]);

        $this->assertSame('user', $user->fresh()->role);
    }

    // -------------------------------------------------------------------------
    // Brute force — login endpoints must be rate limited
    // -------------------------------------------------------------------------

    public function test_web_login_is_rate_limited_after_repeated_failures(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password']);
        }

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password']);

        $response->assertStatus(429);
    }

    public function test_api_login_is_rate_limited_after_repeated_failures(): void
    {
        User::factory()->create(['email' => 'target@example.com']);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/login', ['email' => 'target@example.com', 'password' => 'wrong-password']);
        }

        $response = $this->postJson('/api/v1/login', ['email' => 'target@example.com', 'password' => 'wrong-password']);

        $response->assertStatus(429);
    }

    // -------------------------------------------------------------------------
    // Enumeration — catalog data is not freely readable without a token
    // -------------------------------------------------------------------------

    public function test_mountain_catalog_requires_authentication(): void
    {
        $this->getJson('/api/v1/mountains')->assertStatus(401);
        $this->getJson('/api/v1/mountains/1')->assertStatus(401);
    }

    // -------------------------------------------------------------------------
    // Email verification — posting requires a verified email
    // -------------------------------------------------------------------------

    public function test_unverified_user_cannot_publish_a_forum_post(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->post('/community/posts', [
            'body' => 'Trying to post before verifying my email',
            'category_tags' => ['Hiking Stories'],
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_verified_user_can_publish_a_forum_post(): void
    {
        $user = User::factory()->create(); // verified by default

        $response = $this->actingAs($user)->post('/community/posts', [
            'body' => 'A perfectly valid forum post',
            'category_tags' => ['Hiking Stories'],
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('posts', 1);
    }
}
