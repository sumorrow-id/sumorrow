<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMountainRatingRequest;
use App\Models\Mountain;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class ExploreController extends Controller
{
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

        $seed = $request->input('seed', rand(1, 999999));
        $mountains = $query->inRandomOrder($seed)->paginate(10)->appends(['seed' => $seed])->withQueryString();

        return view('explore', compact('mountains'));
    }

    public function show(int $id): View
    {
        $mountain = Mountain::with(['images', 'province', 'basecamps', 'ratings.user'])->findOrFail($id);

        $nearbyMountains = Mountain::with(['images', 'province'])
            ->where('province_id', $mountain->province_id)
            ->where('id', '!=', $mountain->id)
            ->inRandomOrder()
            ->take(3)
            ->get();

        $weatherUrl = URL::temporarySignedRoute('weather.show', now()->addHours(2), ['mountain' => $mountain->id]);
        $forecastUrl = URL::temporarySignedRoute('weather.forecast', now()->addHours(2), ['mountain' => $mountain->id]);

        return view('explore.show', compact('mountain', 'nearbyMountains', 'weatherUrl', 'forecastUrl'));
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
}
