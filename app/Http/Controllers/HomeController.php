<?php

namespace App\Http\Controllers;

use App\Models\Mountain;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

class HomeController extends Controller
{
    public function index()
    {
        $cached = Cache::remember('mountains.home', now()->addDay(), function () {
            $mountains = Mountain::with(['province', 'images'])->get();
            $fallbackImage = 'https://images.unsplash.com/photo-1627916538356-9a2ebfb1d200?auto=format&fit=crop&q=80&w=500';

            $weatherData = $mountains->map(function ($mountain) {
                $provinceName = str_replace('Provinsi ', '', $mountain->province?->name ?? 'Indonesia');
                $coverImage = $mountain->images->firstWhere('is_cover', true) ?? $mountain->images->first();

                return [
                    'id' => $mountain->id,
                    'loc' => $mountain->name.', '.$provinceName,
                    'url' => '/explore/'.$mountain->id,
                    'image' => $coverImage?->image_url,
                    'temp' => '--&deg;',
                ];
            })->values()->all();

            $allMountains = $mountains->map(function ($mountain) use ($fallbackImage) {
                $provinceName = str_replace('Provinsi ', '', $mountain->province?->name ?? '');
                $firstImage = $mountain->images->first();

                return [
                    'id' => $mountain->id,
                    'name' => $mountain->name,
                    'location' => $provinceName,
                    'elevation' => $mountain->elevation_masl ? $mountain->elevation_masl.' mdpl' : 'Unknown',
                    'difficulty' => $mountain->difficulty ? ucfirst($mountain->difficulty) : 'Moderate',
                    'image' => $firstImage?->image_url ?? $fallbackImage,
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

        $allMountains = collect($cached['allMountains'])->shuffle();

        $popularMountains = $allMountains->take(10)->map(fn ($m) => [
            'id' => $m['id'],
            'name' => $m['name'],
            'location' => $m['location'],
            'image' => $m['image'],
        ]);

        $randomPeaks = $allMountains->slice(10)->take(3)->map(fn ($m) => [
            'id' => $m['id'],
            'name' => $m['name'],
            'location' => $m['location'],
            'elevation' => $m['elevation'],
            'difficulty' => $m['difficulty'],
            'image' => $m['image'],
        ]);

        return view('home', compact('weatherData', 'popularMountains', 'randomPeaks', 'heroImages'));
    }

    public function redirectToHome()
    {
        return redirect()->route('home');
    }
}
