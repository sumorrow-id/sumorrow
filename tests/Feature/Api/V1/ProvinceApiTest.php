<?php

namespace Tests\Feature\Api\V1;

use App\Models\Mountain;
use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProvinceApiTest extends TestCase
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
            'province_id'      => $province->id,
            'name'             => 'Test Mountain',
            'elevation_masl'   => 3000,
            'length_km'        => 10.0,
            'elevation_gain_m' => 1500,
            'coordinates'      => '7.45S 110.44E',
            'description'      => 'A test description.',
            'is_active'        => true,
            'difficulty'       => 'moderate',
            'avg_rating'       => 4.0,
        ], $overrides));
    }

    // -------------------------------------------------------------------------
    // Unauthenticated — all province endpoints should return 401
    // -------------------------------------------------------------------------

    public function test_unauthenticated_index_returns_401(): void
    {
        $this->getJson('/api/v1/provinces')->assertStatus(401);
    }

    public function test_unauthenticated_show_returns_401(): void
    {
        $province = $this->province();
        $this->getJson("/api/v1/provinces/{$province->id}")->assertStatus(401);
    }

    public function test_unauthenticated_mountains_returns_401(): void
    {
        $province = $this->province();
        $this->getJson("/api/v1/provinces/{$province->id}/mountains")->assertStatus(401);
    }

    // -------------------------------------------------------------------------
    // GET /api/v1/provinces — index
    // -------------------------------------------------------------------------

    public function test_index_returns_200_with_data_array(): void
    {
        $this->province('Bali');
        $this->province('Sulawesi Selatan');

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/provinces');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'name', 'mountains_count']],
                'links',
                'meta',
            ]);
    }

    public function test_index_includes_mountains_count(): void
    {
        $prov = $this->province('Jawa Timur');
        $this->mountain($prov);
        $this->mountain($prov, ['name' => 'Peak 2']);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/provinces');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Jawa Timur', 'mountains_count' => 2]);
    }

    public function test_index_with_search_filters_by_name(): void
    {
        $this->province('Jawa Barat');
        $this->province('Kalimantan Timur');

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/provinces?search=jawa');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Jawa Barat'])
            ->assertJsonMissing(['name' => 'Kalimantan Timur']);
    }

    public function test_index_search_with_no_match_returns_empty_data(): void
    {
        $this->province('Bali');

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/provinces?search=zzznomatch');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_index_without_search_returns_all_provinces(): void
    {
        $this->province('Bali');
        $this->province('Papua');
        $this->province('Sulawesi Utara');

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/provinces');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_index_respects_limit_param(): void
    {
        foreach (range(1, 5) as $i) {
            $this->province("Province $i");
        }

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/provinces?limit=2');

        $response->assertStatus(200)
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonCount(2, 'data');
    }

    // -------------------------------------------------------------------------
    // GET /api/v1/provinces/{id} — show
    // -------------------------------------------------------------------------

    public function test_show_returns_province_detail(): void
    {
        $prov = $this->province('Bali');

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson("/api/v1/provinces/{$prov->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'name', 'mountains_count']])
            ->assertJsonPath('data.name', 'Bali');
    }

    public function test_show_returns_404_for_nonexistent_province(): void
    {
        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/provinces/99999');

        $response->assertStatus(404)
            ->assertJsonStructure(['message', 'status']);
    }

    // -------------------------------------------------------------------------
    // GET /api/v1/provinces/{id}/mountains — mountains
    // -------------------------------------------------------------------------

    public function test_mountains_returns_mountains_belonging_to_province(): void
    {
        $prov  = $this->province('Jawa Tengah');
        $other = $this->province('Bali');

        $this->mountain($prov,  ['name' => 'Merbabu']);
        $this->mountain($prov,  ['name' => 'Merapi']);
        $this->mountain($other, ['name' => 'Agung']);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson("/api/v1/provinces/{$prov->id}/mountains");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['name' => 'Merbabu'])
            ->assertJsonFragment(['name' => 'Merapi'])
            ->assertJsonMissing(['name' => 'Agung']);
    }

    public function test_mountains_returns_empty_data_when_no_mountains(): void
    {
        $prov = $this->province('Empty Province');

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson("/api/v1/provinces/{$prov->id}/mountains");

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_mountains_returns_404_for_nonexistent_province(): void
    {
        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/provinces/99999/mountains');

        $response->assertStatus(404);
    }
}
