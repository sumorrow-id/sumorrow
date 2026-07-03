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
