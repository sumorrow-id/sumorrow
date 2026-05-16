<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // GET /profile — auth gate
    // -------------------------------------------------------------------------

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('profile'))->assertRedirect('/login');
    }

    // -------------------------------------------------------------------------
    // GET /profile — authenticated
    // -------------------------------------------------------------------------

    public function test_authenticated_user_can_view_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profile'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.profile');
    }

    public function test_admin_user_can_also_view_profile(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('profile'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.profile');
    }
}
