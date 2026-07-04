<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeCommunity(array $overrides = []): Community
    {
        return Community::create(array_merge([
            'name' => 'Pendaki Nusantara',
            'slug' => 'pendaki-nusantara',
            'description' => 'A community for Indonesian hikers.',
            'privacy' => 'public',
            'image_url' => null,
            'created_by' => User::factory()->create()->id,
        ], $overrides));
    }

    // -------------------------------------------------------------------------
    // index
    // -------------------------------------------------------------------------

    public function test_community_index_is_accessible_to_guests(): void
    {
        $response = $this->get(route('community'));

        $response->assertStatus(200);
        $response->assertViewIs('community.index');
        $response->assertViewHas(['suggestedCommunities', 'posts', 'popularTags']);
    }

    public function test_community_index_lists_suggested_communities(): void
    {
        $this->makeCommunity(['name' => 'Gunung Lovers', 'slug' => 'gunung-lovers']);

        $response = $this->get(route('community'));

        $response->assertStatus(200);
        $response->assertSee('Gunung Lovers');
    }

    public function test_community_feed_excludes_summit_logs(): void
    {
        $user = User::factory()->create();
        // A summit log is a post without any category tags.
        $user->posts()->create(['title' => 'My Summit', 'body' => 'SUMMIT_LOG_BODY']);
        $forumPost = $user->posts()->create(['title' => '', 'body' => 'FORUM_POST_BODY']);
        $forumPost->tags()->create(['keyword' => 'hiking-stories']);

        $response = $this->actingAs($user)->get(route('community'));

        $response->assertOk();
        $response->assertSee('FORUM_POST_BODY');
        $response->assertDontSee('SUMMIT_LOG_BODY');
    }

    public function test_community_forum_leaders_count_excludes_summit_logs(): void
    {
        $user = User::factory()->create();

        $forumPost = $user->posts()->create(['title' => '', 'body' => 'tagged forum post']);
        $forumPost->tags()->create(['keyword' => 'hiking-stories']);

        // Two summit logs (tag-less) that must NOT count toward the ranking.
        $user->posts()->create(['title' => 'Summit A', 'body' => 'log a']);
        $user->posts()->create(['title' => 'Summit B', 'body' => 'log b']);

        $response = $this->actingAs($user)->get(route('community'));

        $response->assertOk();
        $leader = $response->viewData('forumLeaders')->firstWhere('id', $user->id);
        $this->assertSame(1, $leader->posts_count);
    }

    // -------------------------------------------------------------------------
    // join
    // -------------------------------------------------------------------------

    public function test_authenticated_user_can_join_a_community(): void
    {
        $user = User::factory()->create();
        $community = $this->makeCommunity();

        $response = $this->actingAs($user)->post(route('community.join', $community));

        $response->assertRedirect();
        $this->assertDatabaseHas('community_user', [
            'community_id' => $community->id,
            'user_id' => $user->id,
            'role' => 'member',
        ]);
    }

    public function test_joining_twice_does_not_create_duplicate_membership(): void
    {
        $user = User::factory()->create();
        $community = $this->makeCommunity();
        $community->members()->attach($user->id, ['role' => 'member']);

        $this->actingAs($user)->post(route('community.join', $community));

        $this->assertDatabaseCount('community_user', 1);
    }

    public function test_guest_cannot_join_a_community(): void
    {
        $community = $this->makeCommunity();

        $response = $this->post(route('community.join', $community));

        $response->assertRedirect('/login');
        $this->assertDatabaseCount('community_user', 0);
    }

    // -------------------------------------------------------------------------
    // store
    // -------------------------------------------------------------------------

    public function test_authenticated_user_can_create_a_community(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('community.store'), [
            'name' => 'Summit Seekers',
            'description' => 'We chase summits together.',
            'privacy' => 'public',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('communities', [
            'name' => 'Summit Seekers',
            'created_by' => $user->id,
        ]);

        $community = Community::where('name', 'Summit Seekers')->firstOrFail();
        $this->assertDatabaseHas('community_user', [
            'community_id' => $community->id,
            'user_id' => $user->id,
            'role' => 'admin',
        ]);
    }

    public function test_creating_a_community_requires_a_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('community.store'), [
            'description' => 'No name supplied.',
            'privacy' => 'public',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('communities', 0);
    }

    public function test_community_name_must_be_unique(): void
    {
        $user = User::factory()->create();
        $this->makeCommunity(['name' => 'Duplicate Club', 'slug' => 'duplicate-club']);

        $response = $this->actingAs($user)->post(route('community.store'), [
            'name' => 'Duplicate Club',
            'description' => 'Attempting a duplicate name.',
            'privacy' => 'public',
        ]);

        $response->assertSessionHasErrors('name');
    }

    // -------------------------------------------------------------------------
    // leave
    // -------------------------------------------------------------------------

    public function test_authenticated_user_can_leave_a_community(): void
    {
        $user = User::factory()->create();
        $community = $this->makeCommunity();
        $community->members()->attach($user->id, ['role' => 'member']);

        $response = $this->actingAs($user)->post(route('community.leave', $community));

        $response->assertRedirect();
        $this->assertDatabaseMissing('community_user', [
            'community_id' => $community->id,
            'user_id' => $user->id,
        ]);
    }
}
