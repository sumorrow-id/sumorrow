<?php

namespace Tests\Feature;

use App\Models\Community;
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

    /**
     * Posts written inside a community belong to that community's page only —
     * they must never surface on the author's profile, whatever its privacy.
     */
    public function test_profile_posts_tab_excludes_community_posts(): void
    {
        $user = User::factory()->create();

        $forumPost = $user->posts()->create(['title' => '', 'body' => 'a global forum post']);
        $forumPost->tags()->create(['keyword' => 'Hiking Stories']);

        foreach (['public', 'private'] as $privacy) {
            $community = Community::create([
                'name' => ucfirst($privacy).' Climbers',
                'slug' => $privacy.'-climbers',
                'description' => 'A community.',
                'privacy' => $privacy,
                'created_by' => $user->id,
            ]);

            $communityPost = $user->posts()->create([
                'title' => '',
                'body' => 'a '.$privacy.' community post',
                'community_id' => $community->id,
            ]);
            $communityPost->tags()->create(['keyword' => 'Hiking Stories']);
        }

        $response = $this->actingAs($user)->get(route('profile'));

        $response->assertOk();
        $response->assertViewHas('forumPosts', fn ($posts) => $posts->pluck('id')->all() === [$forumPost->id]);
        $response->assertViewHas('hikingPosts', fn ($posts) => $posts->isEmpty());
        $response->assertDontSee('a public community post');
        $response->assertDontSee('a private community post');
    }

    public function test_public_profile_excludes_community_posts(): void
    {
        $hiker = User::factory()->create();
        $community = Community::create([
            'name' => 'Private Climbers',
            'slug' => 'private-climbers',
            'description' => 'A community.',
            'privacy' => 'private',
            'created_by' => $hiker->id,
        ]);

        $communityPost = $hiker->posts()->create([
            'title' => '',
            'body' => 'a private community post',
            'community_id' => $community->id,
        ]);
        $communityPost->tags()->create(['keyword' => 'Hiking Stories']);

        $response = $this->get(route('users.show', $hiker));

        $response->assertOk();
        $response->assertViewHas('forumPosts', fn ($posts) => $posts->isEmpty());
        $response->assertViewHas('summitLogs', fn ($posts) => $posts->isEmpty());
        $response->assertDontSee('a private community post');
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

    // -------------------------------------------------------------------------
    // GET /users/{user} — public profile
    // -------------------------------------------------------------------------

    public function test_guest_can_view_another_users_public_profile(): void
    {
        $hiker = User::factory()->create(['username' => 'Rani Hikes', 'bio' => 'Trail runner.']);

        $response = $this->get(route('users.show', $hiker));

        $response->assertOk();
        $response->assertViewIs('profile.public');
        $response->assertSee('Rani Hikes');
        $response->assertSee('Trail runner.');
    }

    public function test_public_profile_shows_forum_posts_summit_logs_and_reviews(): void
    {
        $hiker = User::factory()->create();

        $forumPost = $hiker->posts()->create(['title' => '', 'body' => 'a forum post']);
        $forumPost->tags()->create(['keyword' => 'Hiking Stories']);
        $summitLog = $hiker->posts()->create(['title' => 'Summit Semeru', 'body' => 'a summit log']);

        $response = $this->get(route('users.show', $hiker));

        $response->assertOk();
        $response->assertViewHas('forumPosts', fn ($posts) => $posts->pluck('id')->all() === [$forumPost->id]);
        $response->assertViewHas('summitLogs', fn ($posts) => $posts->pluck('id')->all() === [$summitLog->id]);
        $response->assertViewHas('reviews');
        $response->assertViewHas('achievements');
    }

    public function test_viewing_your_own_public_profile_redirects_to_your_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('users.show', $user))
            ->assertRedirect(route('profile'));
    }

    public function test_public_profile_never_exposes_the_email_address(): void
    {
        $hiker = User::factory()->create(['email' => 'private@example.com']);

        $this->get(route('users.show', $hiker))->assertDontSee('private@example.com');
    }
}
