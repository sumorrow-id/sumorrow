<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMountainRequest;
use App\Http\Requests\Admin\UpdateMountainRequest;
use App\Models\Mountain;
use App\Models\Province;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MountainController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $totalMountains = Mountain::count();
        $activeMountains = Mountain::where('is_active', true)->count();
        $challengingRoutes = Mountain::whereIn('difficulty', ['hard', 'strenuous'])->count();
        $mountains = Mountain::with('province')
            ->when($search !== '', fn ($query) => $query->where(
                fn ($q) => $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('province', fn ($p) => $p->where('name', 'like', "%{$search}%"))
            ))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.mountain-data', compact('totalMountains', 'activeMountains', 'challengingRoutes', 'mountains', 'search'));
    }

    public function create(): View
    {
        $provinces = Province::orderBy('name')->get();

        return view('admin.mountains.create', compact('provinces'));
    }

    public function store(StoreMountainRequest $request): RedirectResponse
    {
        $mountain = Mountain::create($request->validated());

        return redirect()->route('admin.mountain-data')->with('success', __('admin.mountain_created', ['name' => $mountain->name]));
    }

    public function edit(Mountain $mountain): View
    {
        $provinces = Province::orderBy('name')->get();

        return view('admin.mountains.edit', compact('mountain', 'provinces'));
    }

    public function update(UpdateMountainRequest $request, Mountain $mountain): RedirectResponse
    {
        $mountain->update($request->validated());

        return redirect()->route('admin.mountain-data')->with('success', __('admin.mountain_updated', ['name' => $mountain->name]));
    }

    public function destroy(Mountain $mountain): RedirectResponse
    {
        $mountain->delete();

        return back()->with('success', __('admin.mountain_deleted', ['name' => $mountain->name]));
    }
}
