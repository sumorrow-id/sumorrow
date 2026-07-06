<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\Mountain;
use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // GET /home
    // -------------------------------------------------------------------------

    public function test_home_page_returns_200(): void
    {
        $response = $this->get('/home');

        $response->assertStatus(200);
    }

    public function test_home_returns_correct_view(): void
    {
        $response = $this->get('/home');

        $response->assertViewIs('home');
    }

    public function test_home_page_accessible_without_auth(): void
    {
        $response = $this->get('/home');

        $response->assertStatus(200);
    }

    public function test_home_page_accessible_when_authenticated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/home');

        $response->assertStatus(200);
    }

    public function test_home_only_showcases_mountains_with_real_images(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('mountains/with_image.jpg', 'x');

        $province = Province::create(['name' => 'Jawa Timur']);

        $withImage = $this->makeMountain($province, 'Bergambar');
        $withImage->images()->create(['image_url' => 'mountains/with_image.jpg', 'position' => 0, 'is_cover' => true]);

        // Image row exists but the file does not — the accessor resolves it
        // to the default-mountain fallback, so it must not be showcased.
        $withoutFile = $this->makeMountain($province, 'Tanpa File');
        $withoutFile->images()->create(['image_url' => 'mountains/missing.jpg', 'position' => 0, 'is_cover' => true]);

        $response = $this->get('/home');

        $response->assertStatus(200);
        $response->assertSee('storage/mountains/with_image.jpg');
        $response->assertDontSee('images.unsplash.com');

        // The weather widget cycles every mountain (text only), but the image
        // showcases must exclude mountains without a real image file.
        $showcased = $response->viewData('popularMountains')->pluck('name')
            ->merge($response->viewData('randomPeaks')->pluck('name'));
        $this->assertTrue($showcased->contains('Bergambar'));
        $this->assertFalse($showcased->contains('Tanpa File'));

        $communityImages = $response->viewData('communityImages');
        $this->assertTrue($communityImages->isNotEmpty());
        $this->assertTrue($communityImages->every(fn ($url) => ! str_contains($url, 'default-mountain')));
    }

    public function test_home_image_urls_follow_the_request_host_not_the_cache_builder(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('mountains/with_image.jpg', 'x');

        $province = Province::create(['name' => 'Jawa Timur']);
        $this->makeMountain($province, 'Bergambar')
            ->images()->create(['image_url' => 'mountains/with_image.jpg', 'position' => 0, 'is_cover' => true]);

        // First request primes the day-long mountains.home cache on one host.
        $this->get('http://first.test/home')->assertStatus(200);

        // A visitor on a different origin must get image URLs for THEIR host —
        // cached absolute URLs previously 404'd here and forced the fallback.
        $response = $this->get('http://second.test:8000/home');

        $response->assertStatus(200);
        $response->assertSee('http://second.test:8000/storage/mountains/with_image.jpg', false);
        $response->assertDontSee('http://first.test/storage/', false);
    }

    public function test_home_community_section_shows_global_forum_posts_only(): void
    {
        $user = User::factory()->create(['username' => 'rakadewa']);

        $forumPost = $user->posts()->create(['title' => '', 'body' => 'HOME_FORUM_POST']);
        $forumPost->tags()->create(['keyword' => 'hiking-stories']);

        $community = Community::create([
            'name' => 'Pendaki Nusantara',
            'slug' => 'pendaki-nusantara',
            'description' => 'A community for Indonesian hikers.',
            'privacy' => 'public',
            'created_by' => $user->id,
        ]);
        $communityPost = $user->posts()->create([
            'title' => '',
            'body' => 'HOME_COMMUNITY_POST',
            'community_id' => $community->id,
        ]);
        $communityPost->tags()->create(['keyword' => 'gear-talk']);

        $response = $this->get('/home');

        $response->assertOk();
        $response->assertSee('HOME_FORUM_POST');
        $response->assertSee('rakadewa');
        $response->assertDontSee('HOME_COMMUNITY_POST');
        $response->assertSee(route('community.posts.show', $forumPost));
    }

    public function test_home_community_section_falls_back_to_sample_cards_when_forum_is_empty(): void
    {
        $response = $this->get('/home');

        $response->assertOk();
        $response->assertSee('John Doe');
    }

    private function makeMountain(Province $province, string $name): Mountain
    {
        return Mountain::create([
            'province_id' => $province->id,
            'name' => $name,
            'elevation_masl' => 3000,
            'length_km' => 10.0,
            'elevation_gain_m' => 1500,
            'coordinates' => '8 deg 24\' 51" S, 116 deg 27\' 28" E',
            'description' => 'A test mountain.',
            'is_active' => true,
            'difficulty' => 'moderate',
            'avg_rating' => 4.0,
        ]);
    }

    // -------------------------------------------------------------------------
    // GET / — root redirect
    // -------------------------------------------------------------------------

    public function test_root_redirects_to_home(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('home'));
    }
}
