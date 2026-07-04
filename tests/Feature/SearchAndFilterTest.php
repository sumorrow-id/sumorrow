<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchAndFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_can_be_filtered_by_search_term()
    {
        $user = User::factory()->create();
        // Forum posts must carry a category tag — tag-less posts are summit
        // logs and are excluded from the feed.
        $post1 = $user->posts()->create(['title' => '', 'body' => 'I love hiking up Everest']);
        $post1->tags()->create(['keyword' => 'Hiking Stories']);

        $post2 = $user->posts()->create(['title' => '', 'body' => 'Camping near the lake was peaceful']);
        $post2->tags()->create(['keyword' => 'Hiking Stories']);

        $response = $this->actingAs($user)->get(route('community.forum', ['search' => 'Everest']));

        $response->assertStatus(200);
        $response->assertSee('I love hiking up Everest');
        $response->assertDontSee('Camping near the lake was peaceful');
    }

    public function test_feed_can_be_filtered_by_tag()
    {
        $user = User::factory()->create();
        $post1 = $user->posts()->create(['title' => '', 'body' => 'Mountain gear review']);
        $post1->tags()->create(['keyword' => 'Gear & Equipment']);

        $post2 = $user->posts()->create(['title' => '', 'body' => 'Lost in the woods']);
        $post2->tags()->create(['keyword' => 'Safety & Survival']);

        $response = $this->actingAs($user)->get(route('community.forum', ['tag' => 'Gear & Equipment']));

        $response->assertStatus(200);
        $response->assertSee('Mountain gear review');
        $response->assertDontSee('Lost in the woods');
    }

    public function test_feed_can_be_filtered_by_both_search_and_tag()
    {
        $user = User::factory()->create();
        $post1 = $user->posts()->create(['title' => '', 'body' => 'Mountain gear review']);
        $post1->tags()->create(['keyword' => 'Gear & Equipment']);

        $post2 = $user->posts()->create(['title' => '', 'body' => 'Mountain views are great']);
        $post2->tags()->create(['keyword' => 'Hiking Stories']);

        // Only post1 matches both "Mountain" and "Gear & Equipment"
        $response = $this->actingAs($user)->get(route('community.forum', [
            'search' => 'Mountain',
            'tag' => 'Gear & Equipment',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Mountain gear review');
        $response->assertDontSee('Mountain views are great');
    }
}
