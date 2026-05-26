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

class MountainApiTest extends TestCase
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
            'description' => 'A test mountain description.',
            'is_active' => true,
            'difficulty' => 'moderate',
            'avg_rating' => 3.0,
        ], $overrides));
    }

    private function image(Mountain $mountain, int $position = 0, bool $isCover = true): MountainImage
    {
        return MountainImage::create([
            'mountain_id' => $mountain->id,
            'image_url' => "https://example.com/img{$position}.jpg",
            'source_url' => null,
            'position' => $position,
            'is_cover' => $isCover,
            'uploaded_at' => now(),
        ]);
    }

    private function basecamp(Mountain $mountain, string $name = 'Base Camp A'): Basecamp
    {
        return Basecamp::create([
            'mountain_id' => $mountain->id,
            'name' => $name,
        ]);
    }

    private function rating(Mountain $mountain, User $user, int $score = 4, ?string $review = 'Great hike'): MountainRating
    {
        return MountainRating::create([
            'mountain_id' => $mountain->id,
            'user_id' => $user->id,
            'score' => $score,
            'review' => $review,
        ]);
    }

    // -------------------------------------------------------------------------
    // Unauthenticated — all mountain endpoints should return 401
    // -------------------------------------------------------------------------

    public function test_unauthenticated_index_returns_401(): void
    {
        $this->getJson('/api/v1/mountains')->assertStatus(401);
    }

    public function test_unauthenticated_show_returns_401(): void
    {
        $mountain = $this->mountain($this->province());
        $this->getJson("/api/v1/mountains/{$mountain->id}")->assertStatus(401);
    }

    public function test_unauthenticated_images_returns_401(): void
    {
        $mountain = $this->mountain($this->province());
        $this->getJson("/api/v1/mountains/{$mountain->id}/images")->assertStatus(401);
    }

    public function test_unauthenticated_basecamps_returns_401(): void
    {
        $mountain = $this->mountain($this->province());
        $this->getJson("/api/v1/mountains/{$mountain->id}/basecamps")->assertStatus(401);
    }

    public function test_unauthenticated_ratings_returns_401(): void
    {
        $mountain = $this->mountain($this->province());
        $this->getJson("/api/v1/mountains/{$mountain->id}/ratings")->assertStatus(401);
    }

    // -------------------------------------------------------------------------
    // GET /api/v1/mountains — index: basic
    // -------------------------------------------------------------------------

    public function test_index_returns_200_with_paginated_structure(): void
    {
        $prov = $this->province();
        $this->mountain($prov);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'name', 'province', 'difficulty', 'elevation_masl', 'avg_rating', 'is_active']],
                'links',
                'meta',
            ]);
    }

    public function test_index_with_no_mountains_returns_empty_data(): void
    {
        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains');

        $response->assertStatus(200)->assertJsonCount(0, 'data');
    }

    // -------------------------------------------------------------------------
    // GET /api/v1/mountains — index: search filter
    // -------------------------------------------------------------------------

    public function test_index_search_by_name_returns_matching_mountains(): void
    {
        $prov = $this->province();
        $this->mountain($prov, ['name' => 'Rinjani', 'description' => 'Tall mountain']);
        $this->mountain($prov, ['name' => 'Semeru',  'description' => 'Another mountain']);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains?search=Rinjani');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Rinjani'])
            ->assertJsonMissing(['name' => 'Semeru']);
    }

    public function test_index_search_by_description_returns_matching_mountains(): void
    {
        $prov = $this->province();
        $this->mountain($prov, ['name' => 'Peak A', 'description' => 'volcanic crater']);
        $this->mountain($prov, ['name' => 'Peak B', 'description' => 'jungle trail']);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains?search=volcanic');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Peak A'])
            ->assertJsonMissing(['name' => 'Peak B']);
    }

    public function test_index_search_with_no_match_returns_empty_data(): void
    {
        $prov = $this->province();
        $this->mountain($prov, ['name' => 'Merbabu']);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains?search=zzznomatch');

        $response->assertStatus(200)->assertJsonCount(0, 'data');
    }

    // -------------------------------------------------------------------------
    // GET /api/v1/mountains — index: province_id filter
    // -------------------------------------------------------------------------

    public function test_index_province_id_filter_returns_only_that_province(): void
    {
        $java = $this->province('Jawa');
        $bali = $this->province('Bali');
        $this->mountain($java, ['name' => 'Merapi']);
        $this->mountain($bali, ['name' => 'Agung']);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson("/api/v1/mountains?province_id={$java->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Merapi'])
            ->assertJsonMissing(['name' => 'Agung']);
    }

    public function test_index_province_id_with_nonexistent_id_returns_422(): void
    {
        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains?province_id=99999');

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'status', 'errors']);
    }

    // -------------------------------------------------------------------------
    // GET /api/v1/mountains — index: difficulty filter
    // -------------------------------------------------------------------------

    public function test_index_difficulty_filter_returns_only_matching_difficulty(): void
    {
        $prov = $this->province();
        $this->mountain($prov, ['name' => 'EasyPeak', 'difficulty' => 'easy']);
        $this->mountain($prov, ['name' => 'HardPeak', 'difficulty' => 'hard']);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains?difficulty=easy');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'EasyPeak'])
            ->assertJsonMissing(['name' => 'HardPeak']);
    }

    public function test_index_invalid_difficulty_returns_422(): void
    {
        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains?difficulty=extreme');

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'status', 'errors' => ['difficulty']]);
    }

    // -------------------------------------------------------------------------
    // GET /api/v1/mountains — index: is_active filter
    // -------------------------------------------------------------------------

    public function test_index_is_active_true_returns_only_active_mountains(): void
    {
        $prov = $this->province();
        $this->mountain($prov, ['name' => 'ActivePeak',   'is_active' => true]);
        $this->mountain($prov, ['name' => 'InactivePeak', 'is_active' => false]);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains?is_active=true');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'ActivePeak'])
            ->assertJsonMissing(['name' => 'InactivePeak']);
    }

    public function test_index_is_active_false_returns_only_inactive_mountains(): void
    {
        $prov = $this->province();
        $this->mountain($prov, ['name' => 'ActivePeak',   'is_active' => true]);
        $this->mountain($prov, ['name' => 'InactivePeak', 'is_active' => false]);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains?is_active=false');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'InactivePeak'])
            ->assertJsonMissing(['name' => 'ActivePeak']);
    }

    public function test_index_without_is_active_returns_all_mountains(): void
    {
        $prov = $this->province();
        $this->mountain($prov, ['name' => 'ActivePeak',   'is_active' => true]);
        $this->mountain($prov, ['name' => 'InactivePeak', 'is_active' => false]);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains');

        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    // -------------------------------------------------------------------------
    // GET /api/v1/mountains — index: sort and order
    // -------------------------------------------------------------------------

    public function test_index_sort_by_avg_rating_desc(): void
    {
        $prov = $this->province();
        $this->mountain($prov, ['name' => 'LowRated',  'avg_rating' => 2.0]);
        $this->mountain($prov, ['name' => 'HighRated', 'avg_rating' => 5.0]);
        $this->mountain($prov, ['name' => 'MidRated',  'avg_rating' => 3.5]);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains?sort=avg_rating&order=desc');

        $response->assertStatus(200);
        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertEquals(['HighRated', 'MidRated', 'LowRated'], $names);
    }

    public function test_index_sort_by_elevation_asc(): void
    {
        $prov = $this->province();
        $this->mountain($prov, ['name' => 'High',   'elevation_masl' => 4000]);
        $this->mountain($prov, ['name' => 'Low',    'elevation_masl' => 1000]);
        $this->mountain($prov, ['name' => 'Medium', 'elevation_masl' => 2500]);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains?sort=elevation_masl&order=asc');

        $response->assertStatus(200);
        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertEquals(['Low', 'Medium', 'High'], $names);
    }

    public function test_index_defaults_to_sort_by_name_asc(): void
    {
        $prov = $this->province();
        $this->mountain($prov, ['name' => 'Zebra']);
        $this->mountain($prov, ['name' => 'Alpha']);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains');

        $response->assertStatus(200);
        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertEquals(['Alpha', 'Zebra'], $names);
    }

    public function test_index_invalid_sort_field_returns_422(): void
    {
        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains?sort=hacked_field');

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'status', 'errors' => ['sort']]);
    }

    public function test_index_invalid_order_returns_422(): void
    {
        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains?order=random');

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'status', 'errors' => ['order']]);
    }

    // -------------------------------------------------------------------------
    // GET /api/v1/mountains — index: pagination
    // -------------------------------------------------------------------------

    public function test_index_limit_param_controls_page_size(): void
    {
        $prov = $this->province();
        foreach (range(1, 5) as $i) {
            $this->mountain($prov, ['name' => "Mountain $i"]);
        }

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains?limit=2');

        $response->assertStatus(200)
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonCount(2, 'data');
    }

    public function test_index_limit_above_50_returns_422(): void
    {
        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains?limit=100');

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['limit']]);
    }

    // -------------------------------------------------------------------------
    // GET /api/v1/mountains/{id} — show
    // -------------------------------------------------------------------------

    public function test_show_returns_detail_with_expected_fields(): void
    {
        $prov = $this->province('Bali');
        $mountain = $this->mountain($prov, ['name' => 'Agung', 'avg_rating' => 4.5]);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson("/api/v1/mountains/{$mountain->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id', 'name', 'province', 'difficulty',
                    'elevation_masl', 'length_km', 'elevation_gain_m',
                    'coordinates', 'description', 'is_active', 'closed_since',
                    'avg_rating', 'cover_image',
                    'counts' => ['images', 'basecamps', 'ratings'],
                ],
            ])
            ->assertJsonPath('data.name', 'Agung')
            ->assertJsonPath('data.province.name', 'Bali');
    }

    public function test_show_counts_reflect_related_records(): void
    {
        $prov = $this->province();
        $mountain = $this->mountain($prov);
        $rater = User::factory()->create();

        $this->image($mountain, 0, true);
        $this->image($mountain, 1, false);
        $this->basecamp($mountain);
        $this->rating($mountain, $rater);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson("/api/v1/mountains/{$mountain->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.counts.images', 2)
            ->assertJsonPath('data.counts.basecamps', 1)
            ->assertJsonPath('data.counts.ratings', 1);
    }

    public function test_show_cover_image_is_the_is_cover_image(): void
    {
        $prov = $this->province();
        $mountain = $this->mountain($prov);
        $this->image($mountain, 0, false);

        MountainImage::create([
            'mountain_id' => $mountain->id,
            'image_url' => 'https://example.com/cover.jpg',
            'source_url' => null,
            'position' => 1,
            'is_cover' => true,
            'uploaded_at' => now(),
        ]);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson("/api/v1/mountains/{$mountain->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.cover_image', 'https://example.com/cover.jpg');
    }

    public function test_show_cover_image_falls_back_to_first_image_when_no_is_cover(): void
    {
        $prov = $this->province();
        $mountain = $this->mountain($prov);
        $this->image($mountain, 0, false);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson("/api/v1/mountains/{$mountain->id}");

        $response->assertStatus(200);
        $this->assertNotNull($response->json('data.cover_image'));
    }

    public function test_show_cover_image_is_null_when_no_images(): void
    {
        $prov = $this->province();
        $mountain = $this->mountain($prov);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson("/api/v1/mountains/{$mountain->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.cover_image', null);
    }

    public function test_show_returns_404_for_nonexistent_mountain(): void
    {
        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains/99999');

        $response->assertStatus(404)
            ->assertJsonStructure(['message', 'status']);
    }

    // -------------------------------------------------------------------------
    // GET /api/v1/mountains/{id}/images — images
    // -------------------------------------------------------------------------

    public function test_images_returns_images_ordered_by_position(): void
    {
        $prov = $this->province();
        $mountain = $this->mountain($prov);
        $this->image($mountain, 2, false);
        $this->image($mountain, 0, true);
        $this->image($mountain, 1, false);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson("/api/v1/mountains/{$mountain->id}/images");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');

        $positions = collect($response->json('data'))->pluck('position')->all();
        $this->assertEquals([0, 1, 2], $positions);
    }

    public function test_images_returns_expected_fields(): void
    {
        $prov = $this->province();
        $mountain = $this->mountain($prov);
        $this->image($mountain);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson("/api/v1/mountains/{$mountain->id}/images");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'mountain_id', 'image_url', 'source_url', 'position', 'is_cover', 'uploaded_at']],
            ]);
    }

    public function test_images_returns_empty_data_when_no_images(): void
    {
        $prov = $this->province();
        $mountain = $this->mountain($prov);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson("/api/v1/mountains/{$mountain->id}/images");

        $response->assertStatus(200)->assertJsonCount(0, 'data');
    }

    public function test_images_returns_404_for_nonexistent_mountain(): void
    {
        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains/99999/images');

        $response->assertStatus(404);
    }

    // -------------------------------------------------------------------------
    // GET /api/v1/mountains/{id}/basecamps — basecamps
    // -------------------------------------------------------------------------

    public function test_basecamps_returns_basecamps_for_mountain(): void
    {
        $prov = $this->province();
        $mountain = $this->mountain($prov);
        $other = $this->mountain($prov, ['name' => 'Other Peak']);

        $this->basecamp($mountain, 'Camp Alpha');
        $this->basecamp($mountain, 'Camp Beta');
        $this->basecamp($other, 'Camp Gamma');

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson("/api/v1/mountains/{$mountain->id}/basecamps");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['name' => 'Camp Alpha'])
            ->assertJsonFragment(['name' => 'Camp Beta'])
            ->assertJsonMissing(['name' => 'Camp Gamma']);
    }

    public function test_basecamps_returns_empty_data_when_no_basecamps(): void
    {
        $prov = $this->province();
        $mountain = $this->mountain($prov);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson("/api/v1/mountains/{$mountain->id}/basecamps");

        $response->assertStatus(200)->assertJsonCount(0, 'data');
    }

    public function test_basecamps_returns_404_for_nonexistent_mountain(): void
    {
        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains/99999/basecamps');

        $response->assertStatus(404);
    }

    // -------------------------------------------------------------------------
    // GET /api/v1/mountains/{id}/ratings — ratings
    // -------------------------------------------------------------------------

    public function test_ratings_returns_paginated_ratings_with_user_info(): void
    {
        $prov = $this->province();
        $mountain = $this->mountain($prov);
        $rater = User::factory()->create(['username' => 'hikergirl']);

        $this->rating($mountain, $rater, 5, 'Worth every step.');

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson("/api/v1/mountains/{$mountain->id}/ratings");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'score', 'review', 'user' => ['username', 'avatar_url'], 'created_at']],
                'links',
                'meta',
            ])
            ->assertJsonPath('data.0.user.username', 'hikergirl')
            ->assertJsonPath('data.0.score', 5);
    }

    public function test_ratings_does_not_expose_user_email(): void
    {
        $prov = $this->province();
        $mountain = $this->mountain($prov);
        $rater = User::factory()->create(['email' => 'secret@private.com']);

        $this->rating($mountain, $rater);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson("/api/v1/mountains/{$mountain->id}/ratings");

        $response->assertStatus(200)
            ->assertJsonMissing(['email' => 'secret@private.com']);
    }

    public function test_ratings_returns_most_recent_first(): void
    {
        $prov = $this->province();
        $mountain = $this->mountain($prov);

        $rater1 = User::factory()->create();
        $rater2 = User::factory()->create();

        // Insert older one first, then newer
        $this->travel(-10)->minutes(function () use ($mountain, $rater1) {
            $this->rating($mountain, $rater1, 3, 'Older review');
        });
        $this->rating($mountain, $rater2, 5, 'Newer review');

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson("/api/v1/mountains/{$mountain->id}/ratings");

        $response->assertStatus(200);
        $reviews = collect($response->json('data'))->pluck('review')->all();
        $this->assertEquals(['Newer review', 'Older review'], $reviews);
    }

    public function test_ratings_returns_empty_data_when_no_ratings(): void
    {
        $prov = $this->province();
        $mountain = $this->mountain($prov);

        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson("/api/v1/mountains/{$mountain->id}/ratings");

        $response->assertStatus(200)->assertJsonCount(0, 'data');
    }

    public function test_ratings_returns_404_for_nonexistent_mountain(): void
    {
        $response = $this->actingAs($this->user(), 'sanctum')
            ->getJson('/api/v1/mountains/99999/ratings');

        $response->assertStatus(404);
    }
}
