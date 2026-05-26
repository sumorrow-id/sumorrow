<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexProvinceRequest;
use App\Http\Resources\MountainResource;
use App\Http\Resources\ProvinceResource;
use App\Models\Province;

class ProvinceController extends Controller
{
    public function index(IndexProvinceRequest $request)
    {
        $query = Province::query()->withCount('mountains')->orderBy('name');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->string('search').'%');
        }

        return ProvinceResource::collection(
            $query->paginate($request->perPage())->withQueryString()
        );
    }

    public function show(Province $province)
    {
        $province->loadCount('mountains');

        return ProvinceResource::make($province);
    }

    public function mountains(Province $province)
    {
        $mountains = $province->mountains()
            ->with(['province', 'images'])
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return MountainResource::collection($mountains);
    }
}
