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

        return $this->backToGearTab(__('gear.added_successfully'));
    }

    public function update(UpdateGearRequest $request, Gear $gear): RedirectResponse
    {
        $gear->update($request->validated());

        return $this->backToGearTab(__('gear.updated_successfully'));
    }

    public function destroy(Gear $gear): RedirectResponse
    {
        abort_unless(Auth::user()->can('delete', $gear), 403);

        $gear->delete();

        return $this->backToGearTab(__('gear.deleted_successfully'));
    }

    /**
     * Return to the profile with the Gear tab still selected. `back()` would
     * drop the fragment (browsers strip it from Referer), landing the user on
     * the Posts tab after every add/edit/delete.
     */
    private function backToGearTab(string $message): RedirectResponse
    {
        return redirect()->to(route('profile').'#gear')->with('success', $message);
    }
}
