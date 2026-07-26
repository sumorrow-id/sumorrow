<?php

namespace App\Http\Controllers;

use App\Models\Mountain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
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

        // Community showcase is intentionally dummy so real user posts never
        // surface on the public home page. Photos borrow catalog images.
        // ponytail: static sample cards; wire back to Post if real posts are ever wanted here.
        $fallbackImage = fn (int $i) => $communityImages->get($i % max($communityImages->count(), 1), asset('images/placeholder.svg'));

        $communityCards = collect([
            ['name' => 'Budi Santoso', 'body' => 'Sunrise dari puncak Rinjani kemarin benar-benar bikin merinding. Worth banget 7 jam trekking-nya!', 'likes' => '1.7k', 'comments' => '439'],
            ['name' => 'Siti Rahayu', 'body' => 'Tips buat pemula: mulai naik jam 3 pagi biar kebagian golden sunrise. Jangan lupa headlamp cadangan ya.', 'likes' => '982', 'comments' => '210'],
            ['name' => 'Agus Pratama', 'body' => 'Baru turun dari Prau via Patak Banteng. Lautan awan sama bukit Teletubbies-nya juara sih!', 'likes' => '2.3k', 'comments' => '512'],
            ['name' => 'Dewi Lestari', 'body' => 'Ada rekomendasi sleeping bag buat suhu di bawah 5 derajat? Lagi nyari yang ringan tapi tetap hangat.', 'likes' => '640', 'comments' => '128'],
            ['name' => 'Rizky Ramadhan', 'body' => 'Semeru minggu ini cuacanya lagi labil, kabut sering turun. Selalu pantau info dari basecamp Ranu Pani.', 'likes' => '1.1k', 'comments' => '305'],
            ['name' => 'Putri Anggraini', 'body' => 'Pendakian pertama ke Merbabu via Selo, kaki pegel tapi pemandangannya nggak ada obat. Kapan lagi coba?', 'likes' => '774', 'comments' => '196'],
        ])->map(fn (array $card, int $i) => [
            'url' => url('/community'),
            'username' => $card['name'],
            // Initials avatar from the name, same generator the app uses for users.
            'avatar' => 'https://ui-avatars.com/api/?name='.urlencode($card['name']).'&background=094174&color=fff&bold=true',
            'body' => $card['body'],
            'image' => $fallbackImage($i),
            'likes' => $card['likes'],
            'comments' => $card['comments'],
        ]);

        return view('home', compact('weatherData', 'popularMountains', 'randomPeaks', 'heroImages', 'communityImages', 'communityCards'));
    }

    public function redirectToHome(): RedirectResponse
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
