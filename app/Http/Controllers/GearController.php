<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGearRequest;
use App\Http\Requests\UpdateGearRequest;
use App\Models\Gear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class GearController extends Controller
{
    public function store(StoreGearRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['user_id'] = Auth::id();

        Gear::create($validated);

        return back()->with('success', __('gear.added_successfully'));
    }

    public function update(UpdateGearRequest $request, Gear $gear): RedirectResponse
    {
        $gear->update($request->validated());

        return back()->with('success', __('gear.updated_successfully'));
    }

    public function destroy(Gear $gear): RedirectResponse
    {
        abort_unless(Auth::user()->can('delete', $gear), 403);

        $gear->delete();

        return back()->with('success', __('gear.deleted_successfully'));
    }
}
