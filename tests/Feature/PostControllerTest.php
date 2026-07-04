<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_post_with_body_only()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('community.posts.store'), [
            'body' => 'This is a test post body',
            'category_tags' => ['Hiking Stories'],
        ]);

        $response->assertRedirect(route('community.forum'));
        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'body' => 'This is a test post body',
        ]);
        $this->assertDatabaseHas('post_tags', [
            'keyword' => 'Hiking Stories',
        ]);
    }

    public function test_it_can_create_a_post_with_multiple_images()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $file1 = UploadedFile::fake()->create('mountain1.jpg', 100, 'image/jpeg');
        $file2 = UploadedFile::fake()->create('mountain2.png', 100, 'image/png');

        $response = $this->actingAs($user)->post(route('community.posts.store'), [
            'category_tags' => ['Tips and Trick'],
            'images' => [$file1, $file2],
        ]);

        $response->assertRedirect(route('community.forum'));

        // Post should be created
        $post = Post::where('user_id', $user->id)->first();
        $this->assertNotNull($post);

        // Images should be stored in the DB
        $this->assertDatabaseCount('post_images', 2);

        // Ensure files exist on fake disk
        $image1 = $post->images()->where('position', 1)->first();
        $image2 = $post->images()->where('position', 2)->first();

        Storage::disk('public')->assertExists(str_replace('storage/', '', $image1->image_url));
        Storage::disk('public')->assertExists(str_replace('storage/', '', $image2->image_url));
    }

    public function test_it_fails_validation_when_post_is_completely_empty()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('community.posts.store'), [
            'category_tags' => ['Hiking Stories'],
            // No body and no images
        ]);

        $response->assertSessionHasErrors(['body', 'images']);
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_validation_failure_for_missing_category_flashes_old_body()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('community.posts.store'), [
            'body' => 'Draft I do not want to lose',
            // No category_tags selected
        ]);

        $response->assertSessionHasErrors('category_tags');
        $response->assertSessionHasInput('body', 'Draft I do not want to lose');
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_feed_page_shows_forum_tab_and_save_buttons()
    {
        $user = User::factory()->create();
        $post = $user->posts()->create(['title' => '', 'body' => 'hello forum']);
        $post->tags()->create(['keyword' => 'Hiking Stories']);

        $response = $this->actingAs($user)->get(route('community.forum'));

        $response->assertOk();
        $response->assertSee('Forum');
        $response->assertSee('save-btn');
        $response->assertSee('/community/posts/'.$post->id.'/save');
    }

    public function test_forum_feed_shows_posts_that_have_category_tags()
    {
        $user = User::factory()->create();
        $forumPost = $user->posts()->create(['title' => '', 'body' => 'FORUM_POST_BODY']);
        $forumPost->tags()->create(['keyword' => 'hiking-stories']);

        $response = $this->actingAs($user)->get(route('community.forum'));

        $response->assertOk();
        $response->assertSee('FORUM_POST_BODY');
    }

    public function test_forum_feed_excludes_summit_logs_created_from_profile()
    {
        $user = User::factory()->create();
        // A summit log is a post without any category tags.
        $user->posts()->create(['title' => 'My Summit', 'body' => 'SUMMIT_LOG_BODY']);

        $response = $this->actingAs($user)->get(route('community.forum'));

        $response->assertOk();
        $response->assertDontSee('SUMMIT_LOG_BODY');
    }

    public function test_forum_leaders_count_excludes_summit_logs()
    {
        $user = User::factory()->create();

        $forumPost = $user->posts()->create(['title' => '', 'body' => 'tagged forum post']);
        $forumPost->tags()->create(['keyword' => 'hiking-stories']);

        // Two summit logs (tag-less) that must NOT count toward the ranking.
        $user->posts()->create(['title' => 'Summit A', 'body' => 'log a']);
        $user->posts()->create(['title' => 'Summit B', 'body' => 'log b']);

        $response = $this->actingAs($user)->get(route('community.forum'));

        $response->assertOk();
        $leader = $response->viewData('forumLeaders')->firstWhere('id', $user->id);
        $this->assertSame(1, $leader->posts_count);
    }

    public function test_author_can_delete_own_post_and_image_files_are_removed()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('community.posts.store'), [
            'body' => 'Post to delete',
            'category_tags' => ['Hiking Stories'],
            'images' => [UploadedFile::fake()->create('photo.jpg', 100, 'image/jpeg')],
        ]);

        $post = Post::where('user_id', $user->id)->firstOrFail();
        $imagePath = str_replace('storage/', '', $post->images()->first()->image_url);
        Storage::disk('public')->assertExists($imagePath);

        $response = $this->actingAs($user)->delete(route('community.posts.destroy', $post));

        $response->assertRedirect();
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
        $this->assertDatabaseCount('post_images', 0);
        $this->assertDatabaseCount('post_tags', 0);
        Storage::disk('public')->assertMissing($imagePath);
    }

    public function test_deleting_from_summit_log_detail_redirects_to_activities()
    {
        $user = User::factory()->create();
        $post = $user->posts()->create(['title' => 'My Summit', 'body' => 'summit log body']);

        $response = $this->actingAs($user)->delete(route('community.posts.destroy', $post), [
            'redirect_to_activities' => '1',
        ]);

        $response->assertRedirect(route('profile.posts.index'));
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_user_cannot_delete_another_users_post()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = $owner->posts()->create(['title' => '', 'body' => 'not yours']);

        $response = $this->actingAs($other)->delete(route('community.posts.destroy', $post));

        $response->assertForbidden();
        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    public function test_guest_cannot_delete_a_post()
    {
        $owner = User::factory()->create();
        $post = $owner->posts()->create(['title' => '', 'body' => 'guest cannot touch']);

        $response = $this->delete(route('community.posts.destroy', $post));

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    public function test_feed_shows_delete_button_only_on_own_posts()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        // Both must be real forum posts (tagged) to appear in the feed.
        $ownPost = $user->posts()->create(['title' => '', 'body' => 'my own post']);
        $ownPost->tags()->create(['keyword' => 'hiking-stories']);
        $otherPost = $other->posts()->create(['title' => '', 'body' => 'someone elses post']);
        $otherPost->tags()->create(['keyword' => 'hiking-stories']);

        $response = $this->actingAs($user)->get(route('community.forum'));

        $response->assertOk();
        // The destroy URL equals the show URL, so match the form action attribute specifically
        $response->assertSee('action="'.route('community.posts.destroy', $ownPost->id).'"', false);
        $response->assertDontSee('action="'.route('community.posts.destroy', $otherPost->id).'"', false);
    }

    public function test_composer_repopulates_body_from_old_input()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['_old_input' => [
                'body' => 'My draft text',
                'category_tags' => ['Hiking Stories'],
            ]])
            ->get(route('community.forum'));

        $response->assertOk();
        // The typed text must survive a failed-validation reload.
        $response->assertSee('value="My draft text"', false);
    }
}
