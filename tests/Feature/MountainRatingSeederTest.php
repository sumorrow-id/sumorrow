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

    public function test_is_idempotent_and_leaves_real_members_alone(): void
    {
        $this->kawiButak();
        $member = User::factory()->create();

        $this->seed(MountainRatingSeeder::class);
        $this->seed(MountainRatingSeeder::class);

        $this->assertDatabaseCount('mountain_ratings', 50);
        $this->assertSame(51, User::count());
        $this->assertSame(0, $member->ratings()->count());
    }

    /**
     * The deployed VM runs `composer install --no-dev`, so Faker — and with it
     * `fake()` and `User::factory()` — is absent there. The seeder must build
     * its reviewers without either.
     */
    public function test_creates_reviewers_without_relying_on_faker(): void
    {
        $this->kawiButak();

        $this->seed(MountainRatingSeeder::class);

        $reviewer = User::where('email', 'arif.setiawan@sumorrow.test')->firstOrFail();
        $this->assertSame('arif.setiawan', $reviewer->username);
        $this->assertNull($reviewer->password_hash);
        $this->assertSame(50, User::where('email', 'like', '%@sumorrow.test')->count());

        $source = file_get_contents(database_path('seeders/MountainRatingSeeder.php'));
        $this->assertStringNotContainsString('factory()', $source);
        $this->assertStringNotContainsString('fake()', $source);
    }

    public function test_keeps_a_taken_username_unique(): void
    {
        $this->kawiButak();
        User::factory()->create(['username' => 'arif.setiawan']);

        $this->seed(MountainRatingSeeder::class);

        $reviewer = User::where('email', 'arif.setiawan@sumorrow.test')->firstOrFail();
        $this->assertSame('arif.setiawan0', $reviewer->username);
    }

    public function test_does_nothing_when_the_mountain_is_missing(): void
    {
        $this->seed(MountainRatingSeeder::class);

        $this->assertDatabaseCount('mountain_ratings', 0);
    }
}
