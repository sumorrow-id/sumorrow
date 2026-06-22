<?php

namespace Tests\Feature\Console;

use App\Models\Mountain;
use App\Models\MountainImage;
use App\Models\Province;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FetchMountainImagesTest extends TestCase
{
    use RefreshDatabase;

    private string $jsonPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->jsonPath = sys_get_temp_dir().'/mountains_seeder_test_'.uniqid().'.json';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->jsonPath)) {
            unlink($this->jsonPath);
        }
        parent::tearDown();
    }

    private const UNSPLASH = 'https://images.unsplash.com/photo-123?w=2000';

    private function seedMountain(string $name): Mountain
    {
        $province = Province::create(['name' => 'Jawa Tengah']);

        $mountain = Mountain::create([
            'province_id' => $province->id,
            'name' => $name,
            'elevation_masl' => 3000,
            'length_km' => 10.0,
            'elevation_gain_m' => 1500,
            'coordinates' => '7.45S 110.44E',
            'description' => 'A test description.',
            'is_active' => true,
            'difficulty' => 'moderate',
            'avg_rating' => 4.0,
        ]);

        MountainImage::create([
            'mountain_id' => $mountain->id,
            'position' => 0,
            'image_url' => self::UNSPLASH,
            'is_cover' => true,
        ]);

        return $mountain;
    }

    private function writeJson(string $name): void
    {
        file_put_contents($this->jsonPath, json_encode([
            'mountains' => [[
                'name' => $name,
                'images' => [['image_url' => self::UNSPLASH, 'position' => 0, 'is_cover' => true]],
            ]],
        ]));
    }

    public function test_it_replaces_an_unsplash_cover_with_a_wikipedia_photo(): void
    {
        $this->seedMountain('Slamet');
        $this->writeJson('Slamet');

        Http::fake([
            'id.wikipedia.org/*' => Http::response([
                'thumbnail' => ['source' => 'https://upload.wikimedia.org/wikipedia/commons/1/12/Gunung_Slamet.jpg'],
            ]),
        ]);

        $this->artisan('mountains:fetch-images', ['--file' => $this->jsonPath, '--delay' => 0])
            ->assertSuccessful();

        $this->assertDatabaseHas('mountain_images', [
            'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/1/12/Gunung_Slamet.jpg',
        ]);

        $json = json_decode(file_get_contents($this->jsonPath), true);
        $this->assertSame(
            'https://upload.wikimedia.org/wikipedia/commons/1/12/Gunung_Slamet.jpg',
            $json['mountains'][0]['images'][0]['image_url']
        );
    }

    public function test_dry_run_does_not_write_changes(): void
    {
        $this->seedMountain('Slamet');
        $this->writeJson('Slamet');

        Http::fake([
            'id.wikipedia.org/*' => Http::response([
                'thumbnail' => ['source' => 'https://upload.wikimedia.org/wikipedia/commons/1/12/Gunung_Slamet.jpg'],
            ]),
        ]);

        $this->artisan('mountains:fetch-images', ['--file' => $this->jsonPath, '--dry-run' => true, '--delay' => 0])
            ->assertSuccessful();

        $this->assertDatabaseHas('mountain_images', ['image_url' => self::UNSPLASH]);
        $json = json_decode(file_get_contents($this->jsonPath), true);
        $this->assertSame(self::UNSPLASH, $json['mountains'][0]['images'][0]['image_url']);
    }

    public function test_it_rejects_map_and_locator_images(): void
    {
        $this->seedMountain('Slamet');
        $this->writeJson('Slamet');

        // Wikipedia returns a locator map; Commons search returns nothing usable.
        Http::fake([
            '*api/rest_v1*' => Http::response([
                'thumbnail' => ['source' => 'https://upload.wikimedia.org/wikipedia/commons/9/94/Indonesia_relief_location_map.jpg'],
            ]),
            'commons.wikimedia.org/*' => Http::response(['query' => ['pages' => []]]),
        ]);

        $this->artisan('mountains:fetch-images', ['--file' => $this->jsonPath, '--delay' => 0])
            ->assertSuccessful();

        // The map was rejected, so the placeholder must remain untouched.
        $this->assertDatabaseHas('mountain_images', ['image_url' => self::UNSPLASH]);
    }
}
