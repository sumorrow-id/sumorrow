<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_follow_another_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user1)->postJson(route('users.follow', $user2->id));

        $response->assertStatus(200)
                 ->assertJson(['is_following' => true]);

        $this->assertDatabaseHas('follows', [
            'following_id' => $user2->id,
            'follower_id' => $user1->id,
        ]);
    }

    public function test_user_can_unfollow_another_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Follow first
        $user2->followers()->attach($user1->id);

        $response = $this->actingAs($user1)->postJson(route('users.follow', $user2->id));

        $response->assertStatus(200)
                 ->assertJson(['is_following' => false]);

        $this->assertDatabaseMissing('follows', [
            'following_id' => $user2->id,
            'follower_id' => $user1->id,
        ]);
    }

    public function test_user_cannot_follow_themselves()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('users.follow', $user->id));

        $response->assertStatus(400)
                 ->assertJson(['message' => 'You cannot follow yourself.']);

        $this->assertDatabaseCount('follows', 0);
    }

    public function test_guest_cannot_toggle_follow()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('users.follow', $user->id));

        $response->assertStatus(401);
    }
}
