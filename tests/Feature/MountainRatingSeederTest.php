<?php

namespace Tests\Feature;

use App\Models\Mountain;
use App\Models\Province;
use App\Models\User;
use Database\Seeders\MountainRatingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MountainRatingSeederTest extends TestCase
{
    use RefreshDatabase;

    private function kawiButak(): Mountain
    {
        return Mountain::create([
            'province_id' => Province::create(['name' => 'Jawa Timur'])->id,
            'name' => 'Kawi Butak',
            'elevation_masl' => 2651,
            'length_km' => 12.0,
            'elevation_gain_m' => 1600,
            'coordinates' => '7 deg 55\' 0" S, 112 deg 27\' 0" E',
            'description' => 'Gunung Kawi-Butak.',
            'is_active' => true,
            'difficulty' => 'moderate',
            'avg_rating' => 0,
        ]);
    }

    public function test_seeds_fifty_distinct_raters_and_refreshes_the_average(): void
    {
        $mountain = $this->kawiButak();

        $this->seed(MountainRatingSeeder::class);

        $ratings = $mountain->ratings()->get();
        $this->assertCount(50, $ratings);
        $this->assertCount(50, $ratings->pluck('user_id')->unique());
        $this->assertSame(50, User::count());
        $this->assertTrue($ratings->every(fn ($rating) => $rating->score >= 1 && $rating->score <= 5));
        $this->assertTrue($ratings->every(fn ($rating) => filled($rating->review)));

        $this->assertEqualsWithDelta(
            (float) $ratings->avg('score'),
            (float) $mountain->fresh()->avg_rating,
            0.01
        );
    }

    public function test_is_idempotent_and_reuses_existing_users(): void
    {
        $this->kawiButak();
        User::factory()->count(60)->create();

        $this->seed(MountainRatingSeeder::class);
        $this->seed(MountainRatingSeeder::class);

        $this->assertDatabaseCount('mountain_ratings', 50);
        $this->assertSame(60, User::count());
    }

    public function test_does_nothing_when_the_mountain_is_missing(): void
    {
        $this->seed(MountainRatingSeeder::class);

        $this->assertDatabaseCount('mountain_ratings', 0);
    }
}
