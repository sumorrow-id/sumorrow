<?php

namespace App\Http\Controllers;

use App\Models\Mountain;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        $query = Mountain::with(['images', 'province']);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('description', 'like', '%'.$searchTerm.'%');
            });
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

    public function show($id)
    {
        $mountain = Mountain::with(['images', 'province', 'basecamps', 'ratings.user'])->findOrFail($id);

        $nearbyMountains = Mountain::with(['images', 'province'])
            ->where('province_id', $mountain->province_id)
            ->where('id', '!=', $mountain->id)
            ->inRandomOrder()
            ->take(3)
            ->get();

        return view('explore.show', compact('mountain', 'nearbyMountains'));
    }

    public function storeRating(Request $request, $id)
    {
        $request->validate([
            'score' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $mountain = Mountain::findOrFail($id);

        $mountain->ratings()->updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'score' => $request->score,
                'review' => $request->review,
            ]
        );

        $avgRating = $mountain->ratings()->avg('score');
        $mountain->update(['avg_rating' => $avgRating]);

        return back()->with('success', 'Your review has been submitted successfully.');
    }
}
