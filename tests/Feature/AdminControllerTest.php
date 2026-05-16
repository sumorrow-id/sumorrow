<?php

namespace Tests\Feature;

use App\Models\Mountain;
use App\Models\Post;
use App\Models\PostReply;
use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    private function province(string $name = 'Jawa Tengah'): Province
    {
        return Province::create(['name' => $name]);
    }

    private function mountain(Province $province, array $overrides = []): Mountain
    {
        return Mountain::create(array_merge([
            'province_id'      => $province->id,
            'name'             => 'Test Mountain',
            'elevation_masl'   => 3000,
            'length_km'        => 10.0,
            'elevation_gain_m' => 1500,
            'coordinates'      => '7.45S 110.44E',
            'description'      => 'A test mountain description.',
            'is_active'        => true,
            'difficulty'       => 'moderate',
            'avg_rating'       => 4.0,
        ], $overrides));
    }

    private function makePost(User $author, array $overrides = []): Post
    {
        return Post::create(array_merge([
            'author_id' => $author->id,
            'title'     => 'Sample Post',
            'body'      => 'Sample body content.',
        ], $overrides));
    }

    private function reply(Post $post, User $author): PostReply
    {
        return PostReply::create([
            'post_id'   => $post->id,
            'author_id' => $author->id,
            'content'   => 'A reply.',
        ]);
    }

    // -------------------------------------------------------------------------
    // Auth gate — guests
    // -------------------------------------------------------------------------

    public function test_guest_is_redirected_to_login_for_dashboard(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect('/login');
    }

    public function test_guest_is_redirected_to_login_for_forum_moderation(): void
    {
        $this->get(route('admin.forum-moderation'))->assertRedirect('/login');
    }

    public function test_guest_is_redirected_to_login_for_user_updates(): void
    {
        $this->get(route('admin.user-updates'))->assertRedirect('/login');
    }

    public function test_guest_is_redirected_to_login_for_mountain_data(): void
    {
        $this->get(route('admin.mountain-data'))->assertRedirect('/login');
    }

    // -------------------------------------------------------------------------
    // Role gate — authenticated non-admin
    // -------------------------------------------------------------------------

    public function test_non_admin_user_cannot_access_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertRedirect('/');
        $response->assertSessionHas('error', 'Unauthorized access.');
    }

    public function test_non_admin_user_cannot_access_forum_moderation(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.forum-moderation'))
            ->assertRedirect('/');
    }

    public function test_non_admin_user_cannot_access_user_updates(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.user-updates'))
            ->assertRedirect('/');
    }

    public function test_non_admin_user_cannot_access_mountain_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.mountain-data'))
            ->assertRedirect('/');
    }

    // -------------------------------------------------------------------------
    // Dashboard
    // -------------------------------------------------------------------------

    public function test_admin_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas(['recentUsers', 'newUsersCount', 'forumPostsCount', 'recentPosts']);
    }

    public function test_dashboard_counts_new_users_within_last_seven_days(): void
    {
        $admin = $this->admin();

        // Fresh user — within 7 days.
        User::factory()->create();
        // Old user — created 10 days ago, should NOT count.
        User::factory()->create()->forceFill(['created_at' => now()->subDays(10)])->save();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        // admin + the fresh user = 2, the old user excluded.
        $response->assertViewHas('newUsersCount', 2);
    }

    public function test_dashboard_shows_total_forum_post_count(): void
    {
        $admin = $this->admin();
        $this->makePost($admin);
        $this->makePost($admin, ['title' => 'Another']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertViewHas('forumPostsCount', 2);
    }

    // -------------------------------------------------------------------------
    // Forum moderation
    // -------------------------------------------------------------------------

    public function test_admin_can_view_forum_moderation(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.forum-moderation'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.forum-moderation');
        $response->assertViewHas(['totalPosts', 'postsToday', 'totalReplies', 'recentPosts']);
    }

    public function test_forum_moderation_counts_posts_and_replies(): void
    {
        $admin = $this->admin();
        $post = $this->makePost($admin);
        $this->reply($post, $admin);
        $this->reply($post, $admin);

        $response = $this->actingAs($admin)->get(route('admin.forum-moderation'));

        $response->assertViewHas('totalPosts', 1);
        $response->assertViewHas('totalReplies', 2);
    }

    public function test_forum_moderation_counts_only_todays_posts_in_posts_today(): void
    {
        $admin = $this->admin();
        $this->makePost($admin); // today

        $oldPost = $this->makePost($admin, ['title' => 'Yesterday']);
        $oldPost->forceFill(['created_at' => now()->subDay()])->save();

        $response = $this->actingAs($admin)->get(route('admin.forum-moderation'));

        $response->assertViewHas('postsToday', 1);
    }

    // -------------------------------------------------------------------------
    // User updates
    // -------------------------------------------------------------------------

    public function test_admin_can_view_user_updates(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.user-updates'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.user-updates');
        $response->assertViewHas(['totalUsers', 'newThisWeek', 'newThisMonth', 'recentUsers']);
    }

    public function test_user_updates_total_users_includes_admin(): void
    {
        $admin = $this->admin();
        User::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('admin.user-updates'));

        $response->assertViewHas('totalUsers', 4);
    }

    // -------------------------------------------------------------------------
    // Mountain data
    // -------------------------------------------------------------------------

    public function test_admin_can_view_mountain_data(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.mountain-data'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.mountain-data');
        $response->assertViewHas(['totalMountains', 'activeMountains', 'challengingRoutes', 'mountains']);
    }

    public function test_mountain_data_counts_active_mountains_only(): void
    {
        $admin = $this->admin();
        $province = $this->province();

        $this->mountain($province, ['name' => 'A', 'is_active' => true]);
        $this->mountain($province, ['name' => 'B', 'is_active' => true]);
        $this->mountain($province, ['name' => 'C', 'is_active' => false]);

        $response = $this->actingAs($admin)->get(route('admin.mountain-data'));

        $response->assertViewHas('totalMountains', 3);
        $response->assertViewHas('activeMountains', 2);
    }

    public function test_mountain_data_counts_only_hard_and_strenuous_as_challenging(): void
    {
        $admin = $this->admin();
        $province = $this->province();

        $this->mountain($province, ['name' => 'Easy peak', 'difficulty' => 'easy']);
        $this->mountain($province, ['name' => 'Moderate peak', 'difficulty' => 'moderate']);
        $this->mountain($province, ['name' => 'Hard peak', 'difficulty' => 'hard']);
        $this->mountain($province, ['name' => 'Brutal peak', 'difficulty' => 'strenuous']);

        $response = $this->actingAs($admin)->get(route('admin.mountain-data'));

        $response->assertViewHas('challengingRoutes', 2);
    }
}
