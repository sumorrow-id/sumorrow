<?php

namespace App\Http\Controllers;

use App\Models\Mountain;
use App\Models\Province;
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
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
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

        $mountains = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('explore', compact('mountains'));
    }
}
