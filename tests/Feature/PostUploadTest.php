<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_upload_multiple_images()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/profile/posts', [
            'title' => 'Test Post',
            'body' => 'Test body with multiple files',
            'images' => [
                UploadedFile::fake()->create('photo1.jpg', 10, 'image/jpeg'),
                UploadedFile::fake()->create('photo2.jpg', 10, 'image/jpeg'),
                UploadedFile::fake()->create('photo3.jpg', 10, 'image/jpeg'),
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
        ]);

        $post = \App\Models\Post::first();
        $this->assertEquals(3, $post->images()->count());
    }
}