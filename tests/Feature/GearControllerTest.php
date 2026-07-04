<?php

namespace Tests\Feature;

use App\Models\Gear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GearControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeGear(User $user, array $overrides = []): Gear
    {
        return Gear::create(array_merge([
            'user_id' => $user->id,
            'name' => 'Tent',
            'brand' => 'Eiger',
            'weight_grams' => 2000,
            'category' => 'Shelter',
        ], $overrides));
    }

    // -------------------------------------------------------------------------
    // store
    // -------------------------------------------------------------------------

    public function test_authenticated_user_can_store_gear(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('profile'))
            ->post(route('gears.store'), [
                'name' => 'Sleeping Bag',
                'brand' => 'REI',
                'weight_grams' => 900,
                'category' => 'Sleep System',
            ]);

        $response->assertRedirect(route('profile'));
        $this->assertDatabaseHas('gears', [
            'name' => 'Sleeping Bag',
            'user_id' => $user->id,
        ]);
    }

    public function test_storing_gear_requires_name_weight_and_category(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('profile'))
            ->post(route('gears.store'), []);

        $response->assertSessionHasErrors(['name', 'weight_grams', 'category']);
        $this->assertDatabaseCount('gears', 0);
    }

    public function test_guest_cannot_store_gear(): void
    {
        $response = $this->post(route('gears.store'), [
            'name' => 'Boots',
            'weight_grams' => 1200,
            'category' => 'Footwear',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseCount('gears', 0);
    }

    // -------------------------------------------------------------------------
    // update
    // -------------------------------------------------------------------------

    public function test_owner_can_update_their_gear(): void
    {
        $user = User::factory()->create();
        $gear = $this->makeGear($user);

        $response = $this->actingAs($user)
            ->from(route('profile'))
            ->put(route('gears.update', $gear), [
                'name' => 'Ultralight Tent',
                'brand' => 'Eiger',
                'weight_grams' => 1800,
                'category' => 'Shelter',
            ]);

        $response->assertRedirect(route('profile'));
        $this->assertDatabaseHas('gears', [
            'id' => $gear->id,
            'name' => 'Ultralight Tent',
            'weight_grams' => 1800,
        ]);
    }

    public function test_user_cannot_update_another_users_gear(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $gear = $this->makeGear($owner);

        $response = $this->actingAs($intruder)->put(route('gears.update', $gear), [
            'name' => 'Hijacked',
            'weight_grams' => 100,
            'category' => 'Shelter',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('gears', [
            'id' => $gear->id,
            'name' => 'Tent',
        ]);
    }

    // -------------------------------------------------------------------------
    // destroy
    // -------------------------------------------------------------------------

    public function test_owner_can_delete_their_gear(): void
    {
        $user = User::factory()->create();
        $gear = $this->makeGear($user);

        $response = $this->actingAs($user)
            ->from(route('profile'))
            ->delete(route('gears.destroy', $gear));

        $response->assertRedirect(route('profile'));
        $this->assertDatabaseMissing('gears', ['id' => $gear->id]);
    }

    public function test_user_cannot_delete_another_users_gear(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $gear = $this->makeGear($owner);

        $response = $this->actingAs($intruder)->delete(route('gears.destroy', $gear));

        $response->assertForbidden();
        $this->assertDatabaseHas('gears', ['id' => $gear->id]);
    }
}
