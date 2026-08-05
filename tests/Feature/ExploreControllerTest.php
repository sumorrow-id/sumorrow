<?php

namespace Tests\Feature;

use App\Models\Mountain;
use App\Models\MountainRating;
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
     * @param  array<string, mixed>  $overrides
     */
    private function makeMountain(Province $province, array $overrides = []): Mountain
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
            'avg_rating' => 4.0,
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
            'name' => 'Merbabu',
            'elevation_masl' => 3142,
            'length_km' => 10.0,
            'elevation_gain_m' => 2000,
            'coordinates' => '7.45S 110.44E',
            'description' => 'A beautiful mountain',
            'is_active' => true,
            'difficulty' => 'moderate',
            'avg_rating' => 4.5,
        ]);

        $response = $this->get('/explore');

        $response->assertStatus(200);
        $response->assertSee('Merbabu');
    }

    public function test_explore_search_filters_results(): void
    {
        $province = $this->makeProvince('Jawa Tengah');

        $this->makeMountain($province, ['name' => 'Merbabu', 'description' => 'Green mountain in Central Java']);
        // Description mentions the search term — search must match on name only.
        $this->makeMountain($province, ['name' => 'Lawu',    'description' => 'Often compared to Merbabu by hikers']);

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

    // -------------------------------------------------------------------------
    // Nearby / Others partitioning
    // -------------------------------------------------------------------------

    public function test_explore_without_location_lists_everything_under_others(): void
    {
        $province = $this->makeProvince('Jawa Tengah');
        $this->makeMountain($province, ['name' => 'Merapi', 'coordinates' => '7 deg 32\' 24" S, 110 deg 26\' 24" E']);
        $this->makeMountain($province, ['name' => 'Rinjani', 'coordinates' => '8 deg 24\' 36" S, 116 deg 27\' 36" E']);

        $response = $this->get('/explore');

        $response->assertStatus(200);
        $this->assertFalse($response->viewData('hasLocation'));
        $this->assertTrue($response->viewData('nearbyMountains')->isEmpty());
        $this->assertEqualsCanonicalizing(
            ['Merapi', 'Rinjani'],
            $response->viewData('otherMountains')->pluck('name')->all()
        );
    }

    public function test_explore_with_location_splits_nearby_from_others(): void
    {
        $province = $this->makeProvince('Jawa Tengah');
        // User near Yogyakarta: Merapi (~30 km) is nearby, Rinjani (Lombok) is not.
        $this->makeMountain($province, ['name' => 'Merapi', 'coordinates' => '7 deg 32\' 24" S, 110 deg 26\' 24" E']);
        $this->makeMountain($province, ['name' => 'Rinjani', 'coordinates' => '8 deg 24\' 36" S, 116 deg 27\' 36" E']);

        $response = $this->get('/explore?lat=-7.80&lng=110.36');

        $response->assertStatus(200);
        $this->assertTrue($response->viewData('hasLocation'));
        $this->assertSame(100, $response->viewData('radiusKm'));

        $nearby = $response->viewData('nearbyMountains');
        $this->assertSame(['Merapi'], $nearby->pluck('name')->all());
        $this->assertLessThanOrEqual(100, $nearby->first()->distance_km);

        $this->assertSame(['Rinjani'], $response->viewData('otherMountains')->pluck('name')->all());
    }

    public function test_mountain_with_unparseable_coordinates_is_never_nearby(): void
    {
        $province = $this->makeProvince('Jawa Tengah');
        // Non-DMS string the parser can't read → distance null → always "others".
        $mountain = $this->makeMountain($province, ['name' => 'MysteryPeak', 'coordinates' => '7.45S 110.44E']);

        $this->assertNull($mountain->coordinatesToDecimal());
        $this->assertNull($mountain->distanceKmFrom(-7.80, 110.36));

        $response = $this->get('/explore?lat=-7.80&lng=110.36');

        $this->assertTrue($response->viewData('nearbyMountains')->isEmpty());
        $this->assertSame(['MysteryPeak'], $response->viewData('otherMountains')->pluck('name')->all());
    }

    public function test_others_section_is_paginated_ten_per_page(): void
    {
        $province = $this->makeProvince('Jawa Tengah');
        for ($i = 1; $i <= 12; $i++) {
            $this->makeMountain($province, ['name' => 'Peak '.str_pad((string) $i, 2, '0', STR_PAD_LEFT)]);
        }

        $page1 = $this->get('/explore');
        $page1->assertStatus(200);
        $this->assertCount(10, $page1->viewData('otherMountains'));
        $this->assertTrue($page1->viewData('otherMountains')->hasPages());

        $page2 = $this->get('/explore?page=2');
        $page2->assertStatus(200);
        $this->assertCount(2, $page2->viewData('otherMountains'));
    }

    public function test_mountain_parses_dms_into_decimal_degrees(): void
    {
        $province = $this->makeProvince('Jawa Tengah');
        $mountain = $this->makeMountain($province, ['coordinates' => '8 deg 24\' 36" S, 116 deg 27\' 36" E']);

        $decimal = $mountain->coordinatesToDecimal();

        $this->assertEqualsWithDelta(-8.41, $decimal['lat'], 0.01);
        $this->assertEqualsWithDelta(116.46, $decimal['lng'], 0.01);
    }

    // -------------------------------------------------------------------------
    // Reviews — pagination
    // -------------------------------------------------------------------------

    public function test_reviews_are_paginated_five_per_page(): void
    {
        $mountain = $this->makeMountain($this->makeProvince());

        foreach (User::factory()->count(7)->create() as $user) {
            MountainRating::create([
                'user_id' => $user->id,
                'mountain_id' => $mountain->id,
                'score' => 4,
                'review' => 'Great hike.',
            ]);
        }

        $page1 = $this->get(route('explore.show', $mountain->id));
        $page1->assertStatus(200);
        $this->assertCount(5, $page1->viewData('reviews'));
        $this->assertTrue($page1->viewData('reviews')->hasMorePages());
        // The "see more reviews" button must be rendered when more pages exist.
        $page1->assertSee(__('explore.see_more_reviews'));

        $page2 = $this->get(route('explore.show', $mountain->id).'?reviews=2');
        $page2->assertStatus(200);
        $this->assertCount(2, $page2->viewData('reviews'));
        $this->assertSame(7, $page2->viewData('mountain')->ratings_count);
    }

    // -------------------------------------------------------------------------
    // Reviews — delete
    // -------------------------------------------------------------------------

    public function test_owner_can_delete_their_own_review(): void
    {
        $mountain = $this->makeMountain($this->makeProvince());
        $user = User::factory()->create();
        $rating = MountainRating::create([
            'user_id' => $user->id,
            'mountain_id' => $mountain->id,
            'score' => 5,
            'review' => 'Loved it.',
        ]);

        $response = $this->actingAs($user)
            ->from(route('explore.show', $mountain->id))
            ->delete(route('explore.ratings.destroy', $rating));

        $response->assertRedirect(route('explore.show', $mountain->id));
        $this->assertDatabaseMissing('mountain_ratings', ['id' => $rating->id]);
        // Removing the only review resets the mountain's average.
        $this->assertEquals(0.0, (float) $mountain->fresh()->avg_rating);
    }

    public function test_user_cannot_delete_another_users_review(): void
    {
        $mountain = $this->makeMountain($this->makeProvince());
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $rating = MountainRating::create([
            'user_id' => $owner->id,
            'mountain_id' => $mountain->id,
            'score' => 5,
            'review' => 'Loved it.',
        ]);

        $response = $this->actingAs($intruder)->delete(route('explore.ratings.destroy', $rating));

        $response->assertForbidden();
        $this->assertDatabaseHas('mountain_ratings', ['id' => $rating->id]);
    }

    public function test_guest_cannot_delete_a_review(): void
    {
        $mountain = $this->makeMountain($this->makeProvince());
        $owner = User::factory()->create();
        $rating = MountainRating::create([
            'user_id' => $owner->id,
            'mountain_id' => $mountain->id,
            'score' => 5,
        ]);

        $this->delete(route('explore.ratings.destroy', $rating))->assertRedirect('/login');
        $this->assertDatabaseHas('mountain_ratings', ['id' => $rating->id]);
    }

    public function test_delete_review_button_only_shows_on_own_review(): void
    {
        $mountain = $this->makeMountain($this->makeProvince());
        $owner = User::factory()->create();
        $other = User::factory()->create();
        MountainRating::create([
            'user_id' => $owner->id,
            'mountain_id' => $mountain->id,
            'score' => 5,
            'review' => 'Loved it.',
        ]);

        $this->actingAs($owner)->get(route('explore.show', $mountain->id))
            ->assertSee(__('explore.delete_review'));

        $this->actingAs($other)->get(route('explore.show', $mountain->id))
            ->assertDontSee(__('explore.delete_review'));
    }

    public function test_review_links_to_the_reviewers_public_profile(): void
    {
        $mountain = $this->makeMountain($this->makeProvince());
        $reviewer = User::factory()->create();
        MountainRating::create([
            'user_id' => $reviewer->id,
            'mountain_id' => $mountain->id,
            'score' => 4,
            'review' => 'Nice trail.',
        ]);

        $this->get(route('explore.show', $mountain->id))
            ->assertSee(route('users.show', $reviewer), false);
    }
}
