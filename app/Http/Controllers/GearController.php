<?php

namespace App\Http\Controllers;

use App\Models\Gear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GearController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'weight_grams' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
        ]);

        $validated['user_id'] = Auth::id();

        Gear::create($validated);

        return back()->with('success', __('gear.added_successfully'));
    }

    public function update(Request $request, Gear $gear)
    {
        if ($gear->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'weight_grams' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
        ]);

        $gear->update($validated);

        return back()->with('success', __('gear.updated_successfully'));
    }

    public function destroy(Gear $gear)
    {
        if ($gear->user_id !== Auth::id()) {
            abort(403);
        }

        $gear->delete();

        return back()->with('success', __('gear.deleted_successfully'));
    }
}
