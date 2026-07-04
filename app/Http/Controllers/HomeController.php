<?php

namespace App\Http\Controllers;

use App\Models\Mountain;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

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

        return view('home', compact('weatherData', 'popularMountains', 'randomPeaks', 'heroImages', 'communityImages'));
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
