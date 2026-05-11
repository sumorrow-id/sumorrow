<?php

namespace Tests\Feature;

use App\Models\Mountain;
use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExploreControllerTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Create a Province and return it.
     */
    private function makeProvince(string $name = 'Jawa Tengah'): Province
    {
        return Province::create(['name' => $name]);
    }

    /**
     * Create a Mountain belonging to the given Province.
     *
     * @param  Province  $province
     * @param  array<string, mixed>  $overrides
     */
    private function makeMountain(Province $province, array $overrides = []): Mountain
    {
        return Mountain::create(array_merge([
            'province_id'      => $province->id,
            'name'             => 'Test Mountain',
            'elevation_masl'   => 3000,
            'length_km'        => 10.0,
            'elevation_gain_m' => 1500,
            'coordinates'      => '7.45S 110.44E',
            'description'      => 'A test mountain description.',
            'is_active'        => true,
            'difficulty'       => 'moderate',
            'avg_rating'       => 4.0,
        ], $overrides));
    }

    // -------------------------------------------------------------------------
    // Basic response tests
    // -------------------------------------------------------------------------

    public function test_explore_page_returns_200(): void
    {
        $response = $this->get('/explore');

        $response->assertStatus(200);
    }

    public function test_explore_returns_explore_view(): void
    {
        $response = $this->get('/explore');

        $response->assertViewIs('explore');
    }

    public function test_explore_page_accessible_without_auth(): void
    {
        $response = $this->get('/explore');

        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // Filter smoke tests (no DB records needed — just assert no 500)
    // -------------------------------------------------------------------------

    public function test_explore_with_search_parameter_does_not_error(): void
    {
        $response = $this->get('/explore?search=Rinjani');

        $response->assertStatus(200);
    }

    public function test_explore_with_difficulty_filter_does_not_error(): void
    {
        $response = $this->get('/explore?difficulty[]=easy');

        $response->assertStatus(200);
    }

    public function test_explore_with_region_filter_does_not_error(): void
    {
        $response = $this->get('/explore?region[]=Jawa');

        $response->assertStatus(200);
    }

    public function test_explore_with_multiple_filters_does_not_error(): void
    {
        $response = $this->get('/explore?search=test&difficulty[]=moderate&region[]=Sumatera');

        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // Data presence tests
    // -------------------------------------------------------------------------

    public function test_explore_returns_mountains_in_view(): void
    {
        $province = $this->makeProvince('Jawa Tengah');

        $this->makeMountain($province, [
            'name'             => 'Merbabu',
            'elevation_masl'   => 3142,
            'length_km'        => 10.0,
            'elevation_gain_m' => 2000,
            'coordinates'      => '7.45S 110.44E',
            'description'      => 'A beautiful mountain',
            'is_active'        => true,
            'difficulty'       => 'moderate',
            'avg_rating'       => 4.5,
        ]);

        $response = $this->get('/explore');

        $response->assertStatus(200);
        $response->assertSee('Merbabu');
    }

    public function test_explore_search_filters_results(): void
    {
        $province = $this->makeProvince('Jawa Tengah');

        $this->makeMountain($province, ['name' => 'Merbabu', 'description' => 'Green mountain in Central Java']);
        $this->makeMountain($province, ['name' => 'Lawu',    'description' => 'Mountain on the border of Central and East Java']);

        $response = $this->get('/explore?search=Merbabu');

        $response->assertStatus(200);
        $response->assertSee('Merbabu');
        $response->assertDontSee('Lawu');
    }

    public function test_explore_difficulty_filters_results(): void
    {
        $province = $this->makeProvince('Jawa Tengah');

        $this->makeMountain($province, ['name' => 'EasyPeak',  'difficulty' => 'easy']);
        $this->makeMountain($province, ['name' => 'HardPeak',  'difficulty' => 'hard']);

        $response = $this->get('/explore?difficulty[]=easy');

        $response->assertStatus(200);
        $response->assertSee('EasyPeak');
        $response->assertDontSee('HardPeak');
    }
}
