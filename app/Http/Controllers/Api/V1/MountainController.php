<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexMountainRequest;
use App\Http\Resources\BasecampResource;
use App\Http\Resources\MountainDetailResource;
use App\Http\Resources\MountainImageResource;
use App\Http\Resources\MountainRatingResource;
use App\Http\Resources\MountainResource;
use App\Models\Mountain;
use Illuminate\Database\Eloquent\Builder;

class MountainController extends Controller
{
    public function index(IndexMountainRequest $request)
    {
        $query = Mountain::query()->with(['province', 'images']);

        if ($request->filled('search')) {
            $term = $request->string('search');
            $query->where(function (Builder $q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        }

        if ($request->filled('province_id')) {
            $query->where('province_id', $request->integer('province_id'));
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->string('difficulty'));
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $query->orderBy($request->sort(), $request->order());

        return MountainResource::collection(
            $query->paginate($request->perPage())->withQueryString()
        );
    }

    public function show(Mountain $mountain)
    {
        $mountain->load(['province', 'images'])
            ->loadCount(['images', 'basecamps', 'ratings']);

        return MountainDetailResource::make($mountain);
    }

    public function images(Mountain $mountain)
    {
        $images = $mountain->images()->orderBy('position')->get();

        return MountainImageResource::collection($images);
    }

    public function basecamps(Mountain $mountain)
    {
        $basecamps = $mountain->basecamps()->orderBy('name')->get();

        return BasecampResource::collection($basecamps);
    }

    public function ratings(Mountain $mountain)
    {
        $ratings = $mountain->ratings()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return MountainRatingResource::collection($ratings);
    }
}
