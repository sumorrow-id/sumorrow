<?php

namespace Tests\Feature;

use App\Models\Post;
use Database\Seeders\ForumPostSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumPostSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_tagged_global_forum_posts_and_is_idempotent(): void
    {
        $this->seed(ForumPostSeeder::class);

        $forumPosts = Post::whereNull('community_id')->whereHas('tags')->get();
        $this->assertGreaterThanOrEqual(8, $forumPosts->count());

        // Every seeded post carries likes and an image so both the forum
        // feed and the home cards look alive.
        $this->assertTrue($forumPosts->every(fn (Post $post) => $post->likes()->count() > 0));
        $this->assertTrue($forumPosts->every(fn (Post $post) => $post->images()->count() > 0));

        // Seeded image paths must point at bundled public assets.
        $forumPosts->flatMap->images->each(
            fn ($image) => $this->assertFileExists(public_path($image->image_url))
        );

        // Re-running must not duplicate posts, tags, or likes.
        $postCount = Post::count();
        $this->seed(ForumPostSeeder::class);
        $this->assertSame($postCount, Post::count());
    }
}
