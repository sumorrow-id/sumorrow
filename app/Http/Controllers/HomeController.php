<?php

namespace App\Http\Controllers;

use App\Models\Mountain;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        $cached = Cache::remember('mountains.home.localized', now()->addDay(), function () {
            $mountains = Mountain::with(['province', 'images'])->get();

            $weatherData = $mountains->map(function ($mountain) {
                $provinceName = str_replace('Provinsi ', '', $mountain->province?->name ?? 'Indonesia');

                return [
                    'id' => $mountain->id,
                    'loc' => $mountain->name.', '.$provinceName,
                    'url' => '/explore/'.$mountain->id,
                    'temp' => '--&deg;',
                ];
            })->values()->all();

            $allMountains = $mountains->map(function ($mountain) {
                $provinceName = str_replace('Provinsi ', '', $mountain->province?->name ?? '');
                $rawImage = $mountain->images->first()?->getRawOriginal('image_url');

                return [
                    'id' => $mountain->id,
                    'name' => $mountain->name,
                    'location' => $provinceName,
                    'elevation' => $mountain->elevation_masl,
                    'difficulty' => $mountain->difficulty ?? 'moderate',
                    'image_raw' => $rawImage,
                    'has_real_image' => $this->isRealImage($rawImage),
                ];
            })->values()->all();

            return ['weatherData' => $weatherData, 'allMountains' => $allMountains];
        });

        $weatherData = $cached['weatherData'];
        shuffle($weatherData);

        // Signed URLs generated fresh per page load (not cached) — expire in 2 hours.
        // WeatherController rejects any request without a valid signature.
        $weatherData = array_map(function ($item) {
            $item['weatherUrl'] = URL::temporarySignedRoute('weather.show', now()->addHours(2), ['mountain' => $item['id']]);

            return $item;
        }, $weatherData);

        // Curated hero images live in public/images/hero (hero-1.png ... hero-5.png),
        // independent of the mountain catalog so the hero only shows hand-picked shots.
        $heroImages = collect(range(1, 5))
            ->map(fn ($i) => asset("images/hero/hero-{$i}.png"))
            ->all();

        // The home page is a showcase — only feature mountains that have a
        // real catalog image so no card renders the fallback artwork.
        $allMountains = collect($cached['allMountains'])
            ->filter(fn ($m) => $m['has_real_image'] ?? true)
            ->shuffle();

        $popularMountains = $allMountains->take(10)->map(fn ($m) => [
            'id' => $m['id'],
            'name' => $m['name'],
            'location' => $m['location'],
            'image' => $this->resolveImageUrl($m),
        ]);

        $randomPeaks = $allMountains->slice(10)->take(3)->map(fn ($m) => [
            'id' => $m['id'],
            'name' => $m['name'],
            'location' => $m['location'],
            'elevation' => $m['elevation'],
            'difficulty' => $m['difficulty'],
            'image' => $this->resolveImageUrl($m),
        ]);

        // Catalog images for the community showcase cards (they previously
        // pointed at a hardcoded external stock photo).
        $communityImages = $allMountains->take(8)->map(fn ($m) => $this->resolveImageUrl($m))->values();

        // Community showcase: latest global forum posts (same scope as the
        // forum feed — tagged, not inside a community). Posts without an
        // uploaded image borrow a catalog image so every card has a photo.
        $fallbackImage = fn (int $i) => $communityImages->get($i % max($communityImages->count(), 1), asset('images/placeholder.svg'));

        $communityCards = Post::with(['author', 'images'])
            ->withCount(['likes', 'comments'])
            ->whereNull('community_id')
            ->whereHas('tags')
            ->latest()
            ->take(8)
            ->get()
            ->values()
            ->map(fn (Post $post, int $i) => [
                'url' => route('community.posts.show', $post),
                'username' => $post->author->username,
                'avatar' => asset($post->author->avatar_url ?: 'images/community/profile-blank.jpg'),
                'body' => Str::limit($post->body, 90),
                'image' => $post->images->first() ? asset($post->images->first()->image_url) : $fallbackImage($i),
                'likes' => number_format($post->likes_count),
                'comments' => number_format($post->comments_count),
            ]);

        // Empty forum (fresh install) — keep the section alive with the
        // sample cards it used to hardcode in the view.
        if ($communityCards->isEmpty()) {
            $communityCards = collect(range(0, 7))->map(fn (int $i) => [
                'url' => url('/community'),
                'username' => 'John Doe',
                'avatar' => asset('images/community/profile-blank.jpg'),
                'body' => __('home.sample_post'),
                'image' => $fallbackImage($i),
                'likes' => '1.7k',
                'comments' => '439',
            ]);
        }

        return view('home', compact('weatherData', 'popularMountains', 'randomPeaks', 'heroImages', 'communityImages', 'communityCards'));
    }

    public function redirectToHome()
    {
        return redirect()->route('home');
    }

    /**
     * Whether a stored image value points at something displayable: an
     * external URL, or a file that actually exists on the public disk.
     */
    private function isRealImage(?string $rawImage): bool
    {
        if (! $rawImage) {
            return false;
        }

        if (str_starts_with($rawImage, 'http://') || str_starts_with($rawImage, 'https://') || str_starts_with($rawImage, '/')) {
            return true;
        }

        return Storage::disk('public')->exists($rawImage);
    }

    /**
     * Build the display URL at request time so it always carries the host and
     * port the visitor is actually browsing on.
     *
     * @param  array{image_raw?: string|null, image?: string|null}  $mountain
     */
    private function resolveImageUrl(array $mountain): string
    {
        // 'image' is the pre-refactor cache shape; tolerate it until expiry.
        $raw = $mountain['image_raw'] ?? $mountain['image'] ?? null;

        if (! $raw) {
            return asset('images/default-mountain.jpg');
        }

        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://') || str_starts_with($raw, '/')) {
            return $raw;
        }

        return asset('storage/'.$raw);
    }
}
