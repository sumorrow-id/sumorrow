<?php

namespace Tests\Feature\Api\V1;

use App\Models\Basecamp;
use App\Models\Mountain;
use App\Models\MountainImage;
use App\Models\MountainRating;
use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Integration tests verifying that creating data for one entity
 * does not bleed into, modify, or appear in other entities.
 *
 * Each test group follows the same structure:
 *   1. Set up two independent targets (two mountains, two provinces, etc.)
 *   2. Create data for target A only.
 *   3. Assert target A reflects the change.
 *   4. Assert target B is completely unaffected.
 */
class ApiDataIsolationTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Shared fixtures
    // -------------------------------------------------------------------------

    private User $actor;

    private Province $provinceA;

    private Province $provinceB;

    private Mountain $mountainA;

    private Mountain $mountainB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actor = User::factory()->create();

        $this->provinceA = Province::create(['name' => 'Province A']);
        $this->provinceB = Province::create(['name' => 'Province B']);

        $this->mountainA = $this->makeMountain($this->provinceA, 'Mountain A');
        $this->mountainB = $this->makeMountain($this->provinceB, 'Mountain B');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function makeMountain(Province $province, string $name): Mountain
    {
        return Mountain::create([
            'province_id' => $province->id,
            'name' => $name,
            'elevation_masl' => 3000,
            'length_km' => 10.0,
            'elevation_gain_m' => 1500,
            'coordinates' => '7.45S 110.44E',
            'description' => "Description for {$name}.",
            'is_active' => true,
            'difficulty' => 'moderate',
            'avg_rating' => 0,
        ]);
    }

    private function addImage(Mountain $mountain, int $position = 0): MountainImage
    {
        return MountainImage::create([
            'mountain_id' => $mountain->id,
            'image_url' => "https://example.com/{$mountain->id}-{$position}.jpg",
            'source_url' => null,
            'position' => $position,
            'is_cover' => $position === 0,
            'uploaded_at' => now(),
        ]);
    }

    private function addBasecamp(Mountain $mountain, string $name): Basecamp
    {
        return Basecamp::create([
            'mountain_id' => $mountain->id,
            'name' => $name,
        ]);
    }

    private function addRating(Mountain $mountain, User $user, int $score): MountainRating
    {
        return MountainRating::create([
            'mountain_id' => $mountain->id,
            'user_id' => $user->id,
            'score' => $score,
            'review' => null,
        ]);
    }

    private function asActor(): static
    {
        return $this->actingAs($this->actor, 'sanctum');
    }

    // -------------------------------------------------------------------------
    // Rating creation → avg_rating trigger isolation
    // -------------------------------------------------------------------------

    public function test_rating_created_for_mountain_a_does_not_change_mountain_b_avg_rating(): void
    {
        $rater = User::factory()->create();
        $this->addRating($this->mountainA, $rater, 5);

        // Mountain A avg_rating updated by trigger.
        // PHP serialises whole-number floats without a decimal (e.g. 5.0 → 5 in JSON),
        // so we compare against the integer the JSON decoder returns.
        $this->asActor()
            ->getJson("/api/v1/mountains/{$this->mountainA->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.avg_rating', 5);

        // Mountain B avg_rating still 0
        $this->asActor()
            ->getJson("/api/v1/mountains/{$this->mountainB->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.avg_rating', 0);
    }

    public function test_avg_rating_is_the_average_across_multiple_ratings_for_that_mountain_only(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $this->addRating($this->mountainA, $user1, 4);
        $this->addRating($this->mountainA, $user2, 2);
        $this->addRating($this->mountainA, $user3, 3);
        // avg = (4 + 2 + 3) / 3 = 3.0

        // Noise: ratings on Mountain B must not affect Mountain A's average
        $raterB = User::factory()->create();
        $this->addRating($this->mountainB, $raterB, 5);

        // (4 + 2 + 3) / 3 = 3.0 → serialised as integer 3 in JSON
        $this->asActor()
            ->getJson("/api/v1/mountains/{$this->mountainA->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.avg_rating', 3);
    }

    public function test_rating_list_for_mountain_a_does_not_contain_ratings_from_mountain_b(): void
    {
        $raterA = User::factory()->create();
        $raterB = User::factory()->create();

        $this->addRating($this->mountainA, $raterA, 5);
        $this->addRating($this->mountainB, $raterB, 1);

        $response = $this->asActor()
            ->getJson("/api/v1/mountains/{$this->mountainA->id}/ratings");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.user.username', $raterA->username);
    }

    public function test_rating_count_on_mountain_a_show_is_unaffected_by_ratings_on_mountain_b(): void
    {
        $raterA = User::factory()->create();
        $raterB = User::factory()->create();

        $this->addRating($this->mountainA, $raterA, 4);
        $this->addRating($this->mountainB, $raterB, 4);

        $this->asActor()
            ->getJson("/api/v1/mountains/{$this->mountainA->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.counts.ratings', 1);
    }

    // -------------------------------------------------------------------------
    // Image creation isolation
    // -------------------------------------------------------------------------

    public function test_images_created_for_mountain_a_do_not_appear_in_mountain_b_images(): void
    {
        $this->addImage($this->mountainA, 0);
        $this->addImage($this->mountainA, 1);

        $this->asActor()
            ->getJson("/api/v1/mountains/{$this->mountainB->id}/images")
            ->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_image_count_on_mountain_a_show_is_unaffected_by_images_on_mountain_b(): void
    {
        $this->addImage($this->mountainA, 0);
        $this->addImage($this->mountainB, 0);
        $this->addImage($this->mountainB, 1);

        $this->asActor()
            ->getJson("/api/v1/mountains/{$this->mountainA->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.counts.images', 1);
    }

    public function test_cover_image_on_mountain_a_does_not_bleed_into_mountain_b_list_response(): void
    {
        $this->addImage($this->mountainA, 0);  // is_cover = true

        $response = $this->asActor()->getJson('/api/v1/mountains');

        $response->assertStatus(200);

        $byId = collect($response->json('data'))->keyBy('id');

        $this->assertNotNull($byId[$this->mountainA->id]['cover_image']);
        $this->assertNull($byId[$this->mountainB->id]['cover_image']);
    }

    // -------------------------------------------------------------------------
    // Basecamp creation isolation
    // -------------------------------------------------------------------------

    public function test_basecamp_created_for_mountain_a_does_not_appear_in_mountain_b_basecamps(): void
    {
        $this->addBasecamp($this->mountainA, 'Camp Alpha');

        $this->asActor()
            ->getJson("/api/v1/mountains/{$this->mountainB->id}/basecamps")
            ->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_basecamp_count_on_mountain_a_is_unaffected_by_basecamps_on_mountain_b(): void
    {
        $this->addBasecamp($this->mountainA, 'Camp Alpha');
        $this->addBasecamp($this->mountainB, 'Camp Beta');
        $this->addBasecamp($this->mountainB, 'Camp Gamma');

        $this->asActor()
            ->getJson("/api/v1/mountains/{$this->mountainA->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.counts.basecamps', 1);
    }

    // -------------------------------------------------------------------------
    // Province → mountain isolation
    // -------------------------------------------------------------------------

    public function test_mountain_in_province_a_does_not_appear_in_province_b_mountains(): void
    {
        // setUp already created mountainA in provinceA, mountainB in provinceB
        $this->asActor()
            ->getJson("/api/v1/provinces/{$this->provinceB->id}/mountains")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Mountain B'])
            ->assertJsonMissing(['name' => 'Mountain A']);
    }

    public function test_mountains_count_on_province_a_is_unaffected_by_province_b_mountains(): void
    {
        $this->makeMountain($this->provinceB, 'Extra B Mountain');

        $this->asActor()
            ->getJson("/api/v1/provinces/{$this->provinceA->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.mountains_count', 1);
    }

    public function test_province_index_mountains_count_is_scoped_per_province(): void
    {
        $this->makeMountain($this->provinceA, 'Extra A Mountain');
        $this->makeMountain($this->provinceB, 'Extra B Mountain 1');
        $this->makeMountain($this->provinceB, 'Extra B Mountain 2');

        // provinceA: 2 mountains total (setUp + extra)
        // provinceB: 3 mountains total (setUp + 2 extra)

        $response = $this->asActor()->getJson('/api/v1/provinces');

        $response->assertStatus(200);

        $byId = collect($response->json('data'))->keyBy('id');

        $this->assertEquals(2, $byId[$this->provinceA->id]['mountains_count']);
        $this->assertEquals(3, $byId[$this->provinceB->id]['mountains_count']);
    }

    // -------------------------------------------------------------------------
    // Mountains index filtering isolation
    // -------------------------------------------------------------------------

    public function test_province_id_filter_excludes_other_provinces_completely(): void
    {
        $response = $this->asActor()
            ->getJson("/api/v1/mountains?province_id={$this->provinceA->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Mountain A'])
            ->assertJsonMissing(['name' => 'Mountain B']);
    }

    public function test_difficulty_filter_only_returns_mountains_of_that_difficulty(): void
    {
        $hard = $this->makeMountain($this->provinceA, 'Hard Peak');
        $hard->update(['difficulty' => 'hard']);

        $response = $this->asActor()
            ->getJson('/api/v1/mountains?difficulty=moderate');

        $response->assertStatus(200);
        $names = collect($response->json('data'))->pluck('name')->all();

        $this->assertContains('Mountain A', $names);
        $this->assertContains('Mountain B', $names);
        $this->assertNotContains('Hard Peak', $names);
    }

    public function test_is_active_filter_does_not_affect_inactive_mountains(): void
    {
        $inactive = $this->makeMountain($this->provinceA, 'Closed Peak');
        $inactive->update(['is_active' => false]);

        $response = $this->asActor()
            ->getJson('/api/v1/mountains?is_active=true');

        $response->assertStatus(200);
        $names = collect($response->json('data'))->pluck('name')->all();

        $this->assertNotContains('Closed Peak', $names);
        $this->assertContains('Mountain A', $names);
        $this->assertContains('Mountain B', $names);
    }
}
