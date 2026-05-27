<?php

namespace Tests\Feature\Api\V1;

use App\Models\Basecamp;
use App\Models\Mountain;
use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasecampApiTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function user(): User
    {
        return User::factory()->create();
    }

    private function province(string $name = 'Jawa Tengah'): Province
    {
        return Province::create(['name' => $name]);
    }

    private function mountain(Province $province, array $overrides = []): Mountain
    {
        return Mountain::create(array_merge([
            'province_id' => $province->id,
            'name' => 'Test Mountain',
            'elevation_masl' => 3000,
            'length_km' => 10.0,
            'elevation_gain_m' => 1500,
            'coordinates' => '7.45S 110.44E',
            'description' => 'A test description.',
            'is_active' => true,
            'difficulty' => 'moderate',
            'avg_rating' => 4.0,
        ], $overrides));
    }

    private function basecamp(Mountain $mountain, string $name = 'Camp A'): Basecamp
    {
        return Basecamp::create([
            'mountain_id' => $mountain->id,
            'name' => $name,
        ]);
    }

    // -------------------------------------------------------------------------
    // Unauthenticated — should return 401
    // -------------------------------------------------------------------------

    public function test_unauthenticated_show_returns_401(): void
    {
        $basecamp = $this->basecamp($this->mountain($this->province()));
        $this->getJson("/api/v1/basecamps/{$basecamp->id}")->assertStatus(401);
    }

    // -------------------------------------------------------------------------
    // GET /api/v1/basecamps/{id} — show
    // -------------------------------------------------------------------------

    public function test_show_returns_basecamp_with_mountain_reference(): void
    {
        $prov = $this->province('Jawa Tengah');
        $mountain = $this->mountain($prov, ['name' => 'Merbabu']);
        $basecamp = $this->basecamp($mountain, 'Cunthel');

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson("/api/v1/basecamps/{$basecamp->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'mountain_id', 'name', 'mountain' => ['id', 'name']],
            ])
            ->assertJsonPath('data.name', 'Cunthel')
            ->assertJsonPath('data.mountain.name', 'Merbabu');
    }

    public function test_show_mountain_id_matches_parent_mountain(): void
    {
        $mountain = $this->mountain($this->province());
        $basecamp = $this->basecamp($mountain, 'Lower Camp');

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson("/api/v1/basecamps/{$basecamp->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.mountain_id', $mountain->id);
    }

    public function test_show_returns_404_for_nonexistent_basecamp(): void
    {
        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/basecamps/99999');

        $response->assertStatus(404)
            ->assertJsonStructure(['message', 'status']);
    }
}
