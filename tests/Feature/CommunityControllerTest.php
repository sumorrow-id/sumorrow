<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_my_community_sidebar_uses_profile_image_with_bundled_fallback(): void
    {
        $user = User::factory()->create();

        $withImage = $this->makeCommunity(['name' => 'Punya Gambar', 'slug' => 'punya-gambar', 'image_url' => 'storage/community/custom.jpg']);
        $withoutImage = $this->makeCommunity(['name' => 'Tanpa Gambar', 'slug' => 'tanpa-gambar']);
        $withImage->members()->attach($user->id, ['role' => 'member']);
        $withoutImage->members()->attach($user->id, ['role' => 'member']);

        $response = $this->actingAs($user)->get(route('community'));

        $response->assertOk();
        $response->assertSee('storage/community/custom.jpg');
        $response->assertSee('images/community/profile.avif');
        $response->assertDontSee('bromo.jpg');

        // Each My Community entry links to its detail page.
        $response->assertSee(route('community.show', $withImage));
        $response->assertSee(route('community.show', $withoutImage));
    }

    public function test_my_communities_can_be_searched_and_are_paginated(): void
    {
        $user = User::factory()->create();

        // 7 joined communities → 6 on page one, 1 on page two.
        foreach (range(1, 6) as $i) {
            $this->makeCommunity(['name' => "Rinjani Squad {$i}", 'slug' => "rinjani-squad-{$i}"])
                ->members()->attach($user->id, ['role' => 'member']);
        }
        $this->makeCommunity(['name' => 'Semeru Squad', 'slug' => 'semeru-squad'])
            ->members()->attach($user->id, ['role' => 'member']);

        // Page one holds the first six, not the seventh.
        $response = $this->actingAs($user)->get(route('community', ['tab' => 'community']));
        $response->assertOk();
        $response->assertSee('Rinjani Squad 1');
        $response->assertDontSee('Semeru Squad');

        // Page two holds the seventh, and pagination links keep the tab selected.
        $response = $this->actingAs($user)->get(route('community', ['tab' => 'community', 'community_page' => 2]));
        $response->assertOk();
        $response->assertSee('Semeru Squad');
        $response->assertDontSee('Rinjani Squad 1');

        // Search filters the joined list.
        $response = $this->actingAs($user)->get(route('community', ['tab' => 'community', 'community_search' => 'Semeru']));
        $response->assertOk();
        $response->assertSee('Semeru Squad');
        $response->assertDontSee('Rinjani Squad 1');
    }

    public function test_suggested_communities_exclude_joined_ones_beyond_the_first_page(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 7) as $i) {
            $this->makeCommunity(['name' => "Joined {$i}", 'slug' => "joined-{$i}"])
                ->members()->attach($user->id, ['role' => 'member']);
        }

        // The 7th joined community sits on page two of My Communities, so it must
        // still be excluded from the suggestions on page one.
        $response = $this->actingAs($user)->get(route('community', ['tab' => 'community']));

        $response->assertOk();
        $response->assertDontSee('Joined 7');
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
    // private communities (join token)
    // -------------------------------------------------------------------------

    public function test_creating_a_private_community_generates_a_join_token(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('community.store'), [
            'name' => 'Secret Summit',
            'description' => 'Invite only.',
            'privacy' => 'private',
        ]);

        $community = Community::where('name', 'Secret Summit')->firstOrFail();
        $this->assertNotNull($community->join_token);
    }

    public function test_making_a_community_private_generates_a_join_token(): void
    {
        $creator = User::factory()->create();
        $community = $this->makeCommunity(['created_by' => $creator->id]);

        $this->actingAs($creator)->patch(route('community.update', $community), [
            'name' => $community->name,
            'description' => $community->description,
            'privacy' => 'private',
        ]);

        $this->assertNotNull($community->refresh()->join_token);
    }

    public function test_joining_a_private_community_requires_a_valid_token(): void
    {
        $user = User::factory()->create();
        $community = $this->makeCommunity(['privacy' => 'private', 'join_token' => 'SECRET12']);

        // Wrong token is rejected.
        $response = $this->actingAs($user)->post(route('community.join', $community), [
            'join_token' => 'WRONG999',
        ]);
        $response->assertSessionHasErrors('join_token');
        $this->assertDatabaseMissing('community_user', ['user_id' => $user->id]);

        // Missing token is rejected.
        $this->actingAs($user)->post(route('community.join', $community))
            ->assertSessionHasErrors('join_token');

        // Correct token joins.
        $this->actingAs($user)->post(route('community.join', $community), [
            'join_token' => 'SECRET12',
        ])->assertRedirect(route('community.show', $community));

        $this->assertDatabaseHas('community_user', [
            'community_id' => $community->id,
            'user_id' => $user->id,
            'role' => 'member',
        ]);
    }

    public function test_private_community_content_is_locked_for_non_members(): void
    {
        $user = User::factory()->create();
        $community = $this->makeCommunity(['privacy' => 'private', 'join_token' => 'SECRET12']);

        $response = $this->actingAs($user)->get(route('community.show', $community));

        $response->assertOk();
        $response->assertViewIs('community.show-locked');
        $response->assertDontSee('SECRET12');
    }

    public function test_private_community_content_is_visible_to_members(): void
    {
        $member = User::factory()->create();
        $community = $this->makeCommunity(['privacy' => 'private', 'join_token' => 'SECRET12']);
        $community->members()->attach($member->id, ['role' => 'member']);

        $response = $this->actingAs($member)->get(route('community.show', $community));

        $response->assertOk();
        $response->assertViewIs('community.show');
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

    // -------------------------------------------------------------------------
    // show
    // -------------------------------------------------------------------------

    public function test_authenticated_user_can_view_community_detail_page(): void
    {
        $user = User::factory()->create();
        $community = $this->makeCommunity();

        $response = $this->actingAs($user)->get(route('community.show', $community));

        $response->assertOk();
        $response->assertViewIs('community.show');
        $response->assertSee('Pendaki Nusantara');
    }

    public function test_guest_is_redirected_from_community_detail_page(): void
    {
        $community = $this->makeCommunity();

        $response = $this->get(route('community.show', $community));

        $response->assertRedirect('/login');
    }

    // -------------------------------------------------------------------------
    // update
    // -------------------------------------------------------------------------

    public function test_creator_can_update_their_community(): void
    {
        $creator = User::factory()->create();
        $community = $this->makeCommunity(['created_by' => $creator->id]);

        $response = $this->actingAs($creator)->patch(route('community.update', $community), [
            'name' => 'Updated Name',
            'description' => 'Updated description.',
            'privacy' => 'private',
        ]);

        $response->assertRedirect(route('community.show', $community));
        $this->assertDatabaseHas('communities', [
            'id' => $community->id,
            'name' => 'Updated Name',
            'slug' => 'updated-name',
            'description' => 'Updated description.',
            'privacy' => 'private',
        ]);
    }

    public function test_creator_can_update_community_images(): void
    {
        Storage::fake('public');

        $creator = User::factory()->create();
        $community = $this->makeCommunity(['created_by' => $creator->id]);

        $response = $this->actingAs($creator)->patch(route('community.update', $community), [
            'name' => $community->name,
            'description' => $community->description,
            'privacy' => $community->privacy,
            'profile_image' => UploadedFile::fake()->create('profile.jpg', 100, 'image/jpeg'),
            'banner_image' => UploadedFile::fake()->create('banner.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertSessionHasNoErrors();

        $community->refresh();
        $this->assertStringStartsWith('storage/community/', $community->image_url);
        $this->assertStringStartsWith('storage/community/', $community->banner_url);
        Storage::disk('public')->assertExists(str_replace('storage/', '', $community->image_url));
        Storage::disk('public')->assertExists(str_replace('storage/', '', $community->banner_url));
    }

    public function test_member_cannot_update_someone_elses_community(): void
    {
        $member = User::factory()->create();
        $community = $this->makeCommunity();
        $community->members()->attach($member->id, ['role' => 'member']);

        $response = $this->actingAs($member)->patch(route('community.update', $community), [
            'name' => 'Hijacked Name',
            'description' => 'Should not be saved.',
            'privacy' => 'public',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('communities', [
            'id' => $community->id,
            'name' => 'Pendaki Nusantara',
        ]);
    }

    // -------------------------------------------------------------------------
    // destroy
    // -------------------------------------------------------------------------

    public function test_creator_can_delete_their_community(): void
    {
        $creator = User::factory()->create();
        $community = $this->makeCommunity(['created_by' => $creator->id]);
        $community->members()->attach($creator->id, ['role' => 'admin']);

        $response = $this->actingAs($creator)->delete(route('community.destroy', $community));

        $response->assertRedirect();
        $this->assertDatabaseMissing('communities', ['id' => $community->id]);
        $this->assertDatabaseMissing('community_user', ['community_id' => $community->id]);
    }

    public function test_member_cannot_delete_someone_elses_community(): void
    {
        $member = User::factory()->create();
        $community = $this->makeCommunity();
        $community->members()->attach($member->id, ['role' => 'member']);

        $response = $this->actingAs($member)->delete(route('community.destroy', $community));

        $response->assertForbidden();
        $this->assertDatabaseHas('communities', ['id' => $community->id]);
    }

    // -------------------------------------------------------------------------
    // community posts
    // -------------------------------------------------------------------------

    public function test_member_can_post_inside_a_community(): void
    {
        $member = User::factory()->create();
        $community = $this->makeCommunity();
        $community->members()->attach($member->id, ['role' => 'member']);

        $response = $this->actingAs($member)->post(route('community.posts.store'), [
            'body' => 'COMMUNITY_ONLY_POST',
            'category_tags' => ['Hiking Stories'],
            'community_id' => $community->id,
        ]);

        $response->assertRedirect(route('community.show', $community));
        $this->assertDatabaseHas('posts', [
            'body' => 'COMMUNITY_ONLY_POST',
            'community_id' => $community->id,
        ]);
    }

    public function test_non_member_cannot_post_inside_a_community(): void
    {
        $user = User::factory()->create();
        $community = $this->makeCommunity();

        $response = $this->actingAs($user)->post(route('community.posts.store'), [
            'body' => 'INTRUDER_POST',
            'category_tags' => ['Hiking Stories'],
            'community_id' => $community->id,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_member_can_comment_on_a_community_post(): void
    {
        $member = User::factory()->create();
        $community = $this->makeCommunity();
        $community->members()->attach($member->id, ['role' => 'member']);
        $post = $member->posts()->create(['title' => '', 'body' => 'community post', 'community_id' => $community->id]);

        $response = $this->actingAs($member)->post(route('community.posts.comments.store', $post), [
            'body' => 'A member reply.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('post_comments', ['post_id' => $post->id, 'body' => 'A member reply.']);
    }

    public function test_non_member_cannot_comment_on_a_community_post(): void
    {
        $member = User::factory()->create();
        $outsider = User::factory()->create();
        $community = $this->makeCommunity();
        $community->members()->attach($member->id, ['role' => 'member']);
        $post = $member->posts()->create(['title' => '', 'body' => 'community post', 'community_id' => $community->id]);

        $response = $this->actingAs($outsider)->post(route('community.posts.comments.store', $post), [
            'body' => 'Intruder reply.',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('post_comments', 0);
    }

    public function test_member_can_like_a_community_post(): void
    {
        $member = User::factory()->create();
        $community = $this->makeCommunity();
        $community->members()->attach($member->id, ['role' => 'member']);
        $post = $member->posts()->create(['title' => '', 'body' => 'community post', 'community_id' => $community->id]);

        $response = $this->actingAs($member)->post(route('community.posts.like', $post));

        $response->assertOk();
        $response->assertJson(['liked' => true, 'count' => 1]);
    }

    public function test_non_member_cannot_like_a_community_post(): void
    {
        $member = User::factory()->create();
        $outsider = User::factory()->create();
        $community = $this->makeCommunity();
        $community->members()->attach($member->id, ['role' => 'member']);
        $post = $member->posts()->create(['title' => '', 'body' => 'community post', 'community_id' => $community->id]);

        $response = $this->actingAs($outsider)->post(route('community.posts.like', $post));

        $response->assertForbidden();
        $this->assertDatabaseCount('post_likes', 0);
    }

    public function test_community_posts_are_excluded_from_global_feeds(): void
    {
        $member = User::factory()->create();
        $community = $this->makeCommunity();
        $community->members()->attach($member->id, ['role' => 'member']);

        $communityPost = $member->posts()->create([
            'title' => '',
            'body' => 'COMMUNITY_SCOPED_BODY',
            'community_id' => $community->id,
        ]);
        $communityPost->tags()->create(['keyword' => 'hiking-stories']);

        $this->actingAs($member)->get(route('community'))
            ->assertOk()
            ->assertDontSee('COMMUNITY_SCOPED_BODY');

        $this->actingAs($member)->get(route('community.show', $community))
            ->assertOk()
            ->assertSee('COMMUNITY_SCOPED_BODY');
    }
}
