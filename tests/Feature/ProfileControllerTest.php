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

    public function test_profile_separates_forum_posts_from_hiking_posts(): void
    {
        $user = User::factory()->create();

        $forumPost = $user->posts()->create(['title' => '', 'body' => 'a forum post']);
        $forumPost->tags()->create(['keyword' => 'Hiking Stories']);

        $hikingPost = $user->posts()->create(['title' => 'Summit Semeru', 'body' => 'a summit log']);

        $response = $this->actingAs($user)->get(route('profile'));

        $response->assertStatus(200);
        $response->assertViewHas('forumPosts', function ($posts) use ($forumPost) {
            return $posts->count() === 1 && $posts->first()->id === $forumPost->id;
        });
        $response->assertViewHas('hikingPosts', function ($posts) use ($hikingPost) {
            return $posts->count() === 1 && $posts->first()->id === $hikingPost->id;
        });
        $response->assertViewHas('lastReviews');
    }

    public function test_profile_post_delete_uses_confirmation_modal(): void
    {
        $user = User::factory()->create();

        $forumPost = $user->posts()->create(['title' => '', 'body' => 'a forum post']);
        $forumPost->tags()->create(['keyword' => 'Hiking Stories']);

        $response = $this->actingAs($user)->get(route('profile'));

        $response->assertOk();
        // Delete goes through the styled confirmation modal, not the native confirm()
        $response->assertSee('confirm-submit-form', false);
        $response->assertSee('data-confirm-message', false);
        $response->assertDontSee('onsubmit="return confirm', false);
    }

    public function test_admin_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('profile'));

        $response->assertRedirect(route('admin.dashboard'));
    }
}
