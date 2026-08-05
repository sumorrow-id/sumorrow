<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Database\Seeders\AchievementSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePostControllerTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // index
    // -------------------------------------------------------------------------

    public function test_index_displays_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profile.posts.index'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.posts.index');
        $response->assertViewHas('posts');
    }

    public function test_index_excludes_forum_posts(): void
    {
        $user = User::factory()->create();

        $forumPost = $user->posts()->create(['title' => '', 'body' => 'forum only']);
        $forumPost->tags()->create(['keyword' => 'Hiking Stories']);

        $user->posts()->create(['title' => 'Summit Log Entry', 'body' => 'hiking log']);

        $response = $this->actingAs($user)->get(route('profile.posts.index'));

        $response->assertStatus(200);
        $response->assertViewHas('posts', function ($posts) {
            return $posts->count() === 1 && $posts->first()->title === 'Summit Log Entry';
        });
    }

    public function test_guest_cannot_view_profile_posts_index(): void
    {
        $response = $this->get(route('profile.posts.index'));

        $response->assertRedirect('/login');
    }

    // -------------------------------------------------------------------------
    // create
    // -------------------------------------------------------------------------

    public function test_create_form_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profile.posts.create'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.posts.create');
        $response->assertViewHas('mountains');
    }

    // -------------------------------------------------------------------------
    // store
    // -------------------------------------------------------------------------

    public function test_authenticated_user_can_store_a_post(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('profile.posts.store'), [
            'title' => 'Summit of Semeru',
            'body' => 'An unforgettable climb to the roof of Java.',
        ]);

        $response->assertRedirect(route('profile.posts.index'));
        $this->assertDatabaseHas('posts', [
            'title' => 'Summit of Semeru',
            'author_id' => $user->id,
        ]);
    }

    public function test_storing_a_summit_log_unlocks_the_first_summit_achievement(): void
    {
        $this->seed(AchievementSeeder::class);
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('profile.posts.store'), [
            'title' => 'Summit Semeru',
            'body' => 'Made it to Mahameru at sunrise.',
        ]);

        $this->assertTrue($user->achievements()->where('title', 'First Summit')->exists());
    }

    public function test_storing_a_post_requires_title_and_body(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('profile.posts.store'), []);

        $response->assertSessionHasErrors(['title', 'body']);
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_storing_a_post_persists_uploaded_images(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('profile.posts.store'), [
            'title' => 'Trip with photos',
            'body' => 'A photo dump from the trail.',
            'images' => [
                UploadedFile::fake()->create('one.jpg', 100, 'image/jpeg'),
                UploadedFile::fake()->create('two.png', 100, 'image/png'),
            ],
        ]);

        $response->assertRedirect(route('profile.posts.index'));

        $post = Post::where('author_id', $user->id)->firstOrFail();
        $this->assertDatabaseCount('post_images', 2);
        Storage::disk('public')->assertExists($post->images()->first()->image_url);
    }

    // -------------------------------------------------------------------------
    // show
    // -------------------------------------------------------------------------

    public function test_user_can_view_their_own_post(): void
    {
        $user = User::factory()->create();
        $post = $user->posts()->create([
            'title' => 'My Private Journal',
            'body' => 'Body text for my own post.',
        ]);

        $response = $this->actingAs($user)->get(route('profile.posts.show', $post));

        $response->assertStatus(200);
        $response->assertViewIs('profile.posts.show');
        $response->assertSee('My Private Journal');
    }

    public function test_user_cannot_view_another_users_post(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = $owner->posts()->create([
            'title' => 'Not Yours',
            'body' => 'This belongs to someone else.',
        ]);

        $response = $this->actingAs($other)->get(route('profile.posts.show', $post));

        $response->assertNotFound();
    }
}
