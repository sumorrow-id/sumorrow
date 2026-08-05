<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMountainRatingRequest;
use App\Models\Mountain;
use App\Models\MountainRating;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class ExploreController extends Controller
{
    /**
     * How close a mountain must be to the visitor to count as "nearby".
     * 100 km covers the mountains reachable from most Indonesian cities
     * (e.g. Jakarta → Gede/Salak, Yogyakarta → Merapi) without spilling
     * across a whole island.
     */
    private const NEARBY_RADIUS_KM = 100;

    public function index(Request $request): View
    {
        $query = Mountain::with(['images', 'province']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('elevation')) {
            $elevation = (int) $request->elevation;
            if ($elevation > 0) {
                // Buffer +/- 300 mdpl
                $buffer = 300;
                $query->whereBetween('elevation_masl', [$elevation - $buffer, $elevation + $buffer]);
            }
        }

        if ($request->filled('difficulty')) {
            $query->whereIn('difficulty', $request->difficulty);
        }

        if ($request->filled('region')) {
            $query->whereHas('province', function (Builder $q) use ($request) {
                $q->where(function (Builder $subQ) use ($request) {
                    foreach ((array) $request->region as $region) {
                        if ($region === 'Sumatera') {
                            $subQ->orWhere('name', 'like', '%Sumatera%')
                                ->orWhere('name', 'like', '%Aceh%')
                                ->orWhere('name', 'like', '%Riau%')
                                ->orWhere('name', 'like', '%Jambi%')
                                ->orWhere('name', 'like', '%Bengkulu%')
                                ->orWhere('name', 'like', '%Lampung%')
                                ->orWhere('name', 'like', '%Bangka%');
                        } elseif ($region === 'Jawa') {
                            $subQ->orWhere('name', 'like', '%Jawa%')
                                ->orWhere('name', 'like', '%Banten%')
                                ->orWhere('name', 'like', '%Jakarta%')
                                ->orWhere('name', 'like', '%Yogyakarta%');
                        } elseif ($region === 'Kalimantan') {
                            $subQ->orWhere('name', 'like', '%Kalimantan%');
                        } elseif ($region === 'Sulawesi') {
                            $subQ->orWhere('name', 'like', '%Sulawesi%')
                                ->orWhere('name', 'like', '%Gorontalo%');
                        } elseif ($region === 'Maluku') {
                            $subQ->orWhere('name', 'like', '%Maluku%');
                        } elseif ($region === 'Papua') {
                            $subQ->orWhere('name', 'like', '%Papua%');
                        } elseif ($region === 'Bali & Nusa Tenggara') {
                            $subQ->orWhere('name', 'like', '%Bali%')
                                ->orWhere('name', 'like', '%Nusa Tenggara%');
                        }
                    }
                });
            });
        }

        // Only 100-ish rows, and "nearby" needs every mountain's coordinates to
        // sort by distance, so partition in PHP rather than paginate in SQL.
        // ponytail: fetch-all is fine at this catalog size; add SQL-side geo
        // filtering only if the catalog grows into the thousands.
        $mountains = $query->get();

        $latitude = $request->filled('lat') ? (float) $request->input('lat') : null;
        $longitude = $request->filled('lng') ? (float) $request->input('lng') : null;
        $hasLocation = $latitude !== null && $longitude !== null;

        $radiusKm = self::NEARBY_RADIUS_KM;
        $nearbyMountains = collect();
        $otherMountains = $mountains->sortBy('name')->values();

        if ($hasLocation) {
            $mountains->each(fn (Mountain $mountain) => $mountain->distance_km = $mountain->distanceKmFrom($latitude, $longitude));

            $nearbyMountains = $mountains
                ->filter(fn (Mountain $mountain) => $mountain->distance_km !== null && $mountain->distance_km <= $radiusKm)
                ->sortBy('distance_km')
                ->values();

            $nearbyIds = $nearbyMountains->pluck('id')->all();
            $otherMountains = $mountains
                ->reject(fn (Mountain $mountain) => in_array($mountain->id, $nearbyIds, true))
                ->sortBy('name')
                ->values();
        }

        // Paginate the "Others" collection (nearby is a page-1 highlight only).
        $perPage = 10;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $otherMountains = (new LengthAwarePaginator(
            $otherMountains->forPage($page, $perPage)->values(),
            $otherMountains->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        ))->withQueryString();

        return view('explore', compact('nearbyMountains', 'otherMountains', 'hasLocation', 'radiusKm'));
    }

    /**
     * How many reviews one "page" of the reviews section shows.
     */
    private const REVIEWS_PER_PAGE = 5;

    public function show(int $id): View
    {
        $mountain = Mountain::with(['images', 'province', 'basecamps'])
            ->withCount('ratings')
            ->findOrFail($id);

        // Reviews are paged rather than capped at 5 so the "see more reviews"
        // button can walk the whole list without loading every row up front.
        $reviews = $mountain->ratings()
            ->with('user')
            ->latest('created_at')
            ->paginate(self::REVIEWS_PER_PAGE, ['*'], 'reviews')
            ->withQueryString()
            ->fragment('reviews');

        $nearbyMountains = Mountain::with(['images', 'province'])
            ->where('province_id', $mountain->province_id)
            ->where('id', '!=', $mountain->id)
            ->inRandomOrder()
            ->take(3)
            ->get();

        $weatherUrl = URL::temporarySignedRoute('weather.show', now()->addHours(2), ['mountain' => $mountain->id]);
        $forecastUrl = URL::temporarySignedRoute('weather.forecast', now()->addHours(2), ['mountain' => $mountain->id]);

        return view('explore.show', compact('mountain', 'reviews', 'nearbyMountains', 'weatherUrl', 'forecastUrl'));
    }

    public function storeRating(StoreMountainRatingRequest $request, int $id): RedirectResponse
    {
        $mountain = Mountain::findOrFail($id);

        // Upsert the rating and recompute the average together so two
        // concurrent submissions can't interleave and store a stale average.
        // ponytail: a transaction narrows the window; a per-mountain lock would
        // close it fully if contention ever matters.
        DB::transaction(function () use ($mountain, $request) {
            $mountain->ratings()->updateOrCreate(
                ['user_id' => auth()->id()],
                [
                    'score' => $request->score,
                    'review' => $request->review,
                ]
            );

            $mountain->update(['avg_rating' => $mountain->ratings()->avg('score')]);
        });

        return back()->with('success', __('explore.review_submitted'));
    }

    /**
     * Delete the authenticated user's own review of a mountain.
     */
    public function destroyRating(Request $request, MountainRating $rating): RedirectResponse
    {
        abort_unless($rating->user_id === $request->user()->id, 403);

        $mountain = $rating->mountain;

        DB::transaction(function () use ($rating, $mountain) {
            $rating->delete();

            $mountain->update(['avg_rating' => $mountain->ratings()->avg('score') ?? 0]);
        });

        return back()->with('success', __('explore.review_deleted'));
    }
}
