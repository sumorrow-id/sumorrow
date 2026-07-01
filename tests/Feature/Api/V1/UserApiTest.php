<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Authentication guard
    // -------------------------------------------------------------------------

    public function test_user_endpoint_requires_authentication(): void
    {
        $this->getJson('/api/v1/user')->assertStatus(401);
    }

    public function test_check_verification_endpoint_requires_authentication(): void
    {
        $this->getJson('/api/v1/check-verification')->assertStatus(401);
    }

    // -------------------------------------------------------------------------
    // show
    // -------------------------------------------------------------------------

    public function test_authenticated_user_can_fetch_their_profile(): void
    {
        $user = User::factory()->create(['username' => 'pendaki_sejati']);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/user');

        $response->assertStatus(200);
        $response->assertJson(['username' => 'pendaki_sejati']);
    }

    public function test_user_endpoint_never_leaks_the_password_hash(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/user');

        $response->assertStatus(200);
        $response->assertJsonMissingPath('password_hash');
    }

    // -------------------------------------------------------------------------
    // checkVerification
    // -------------------------------------------------------------------------

    public function test_check_verification_reports_verified_status(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/check-verification');

        $response->assertStatus(200);
        $response->assertJsonStructure(['verified']);
    }
}
