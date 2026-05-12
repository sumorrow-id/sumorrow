<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // GET /home
    // -------------------------------------------------------------------------

    public function test_home_page_returns_200(): void
    {
        $response = $this->get('/home');

        $response->assertStatus(200);
    }

    public function test_home_returns_correct_view(): void
    {
        $response = $this->get('/home');

        $response->assertViewIs('home');
    }

    public function test_home_page_accessible_without_auth(): void
    {
        $response = $this->get('/home');

        $response->assertStatus(200);
    }

    public function test_home_page_accessible_when_authenticated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/home');

        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // GET / — root redirect
    // -------------------------------------------------------------------------

    public function test_root_redirects_to_home(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('home'));
    }
}
