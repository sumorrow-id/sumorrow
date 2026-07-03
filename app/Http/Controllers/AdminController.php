<?php

namespace App\Http\Controllers;

use App\Models\Mountain;
use App\Models\Province;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $totalUsers = User::count();
        $newUsersCount = User::where('created_at', '>=', now()->subDays(7))->count();
        $totalMountains = Mountain::count();
        $activeMountains = Mountain::where('is_active', true)->count();
        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact('totalUsers', 'newUsersCount', 'totalMountains', 'activeMountains', 'recentUsers'));
    }

    /*
    --------------------------------------------------------------------------
    User management
    --------------------------------------------------------------------------
    */

    public function userUpdates(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $totalUsers = User::count();
        $newThisWeek = User::where('created_at', '>=', now()->subDays(7))->count();
        $newThisMonth = User::where('created_at', '>=', now()->startOfMonth())->count();
        $recentUsers = User::query()
            ->when($search !== '', fn ($query) => $query->where(
                fn ($q) => $q->where('username', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")
            ))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.user-updates', compact('totalUsers', 'newThisWeek', 'newThisMonth', 'recentUsers', 'search'));
    }

    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:user,admin'],
        ]);

        if ($user->id === $request->user()->id) {
            return back()->with('error', __('admin.cannot_change_own_role'));
        }

        // 'role' is guarded against mass assignment; set it explicitly on this admin-only path.
        $user->role = $validated['role'];
        $user->save();

        return back()->with('success', __('admin.role_updated', ['username' => $user->username, 'role' => $user->role]));
    }

    public function destroyUser(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', __('admin.cannot_delete_own_account'));
        }

        $user->delete();

        return back()->with('success', __('admin.user_deleted', ['username' => $user->username]));
    }

    public function exportUsers(): StreamedResponse
    {
        $filename = 'sumorrow-users-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Username', 'Email', 'Role', 'Joined At']);

            User::latest()->chunk(200, function ($users) use ($handle) {
                foreach ($users as $user) {
                    fputcsv($handle, [
                        $user->username,
                        $user->email,
                        $user->role,
                        $user->created_at?->toDateTimeString(),
                    ]);
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /*
    --------------------------------------------------------------------------
    Mountain management
    --------------------------------------------------------------------------
    */

    public function mountainData(Request $request): View
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

    public function createMountain(): View
    {
        $provinces = Province::orderBy('name')->get();

        return view('admin.mountains.create', compact('provinces'));
    }

    public function storeMountain(Request $request): RedirectResponse
    {
        $data = $this->validateMountain($request);

        $mountain = Mountain::create($data);

        return redirect()->route('admin.mountain-data')->with('success', __('admin.mountain_created', ['name' => $mountain->name]));
    }

    public function editMountain(Mountain $mountain): View
    {
        $provinces = Province::orderBy('name')->get();

        return view('admin.mountains.edit', compact('mountain', 'provinces'));
    }

    public function updateMountain(Request $request, Mountain $mountain): RedirectResponse
    {
        $data = $this->validateMountain($request);

        $mountain->update($data);

        return redirect()->route('admin.mountain-data')->with('success', __('admin.mountain_updated', ['name' => $mountain->name]));
    }

    public function destroyMountain(Mountain $mountain): RedirectResponse
    {
        $mountain->delete();

        return back()->with('success', __('admin.mountain_deleted', ['name' => $mountain->name]));
    }

    /**
     * @return array<string, mixed>
     */
    private function validateMountain(Request $request): array
    {
        $data = $request->validate([
            'province_id' => ['required', 'integer', 'exists:provinces,id'],
            'name' => ['required', 'string', 'max:255'],
            'elevation_masl' => ['required', 'integer', 'min:0', 'max:9000'],
            'length_km' => ['required', 'numeric', 'min:0'],
            'elevation_gain_m' => ['required', 'integer', 'min:0'],
            'coordinates' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'difficulty' => ['required', 'in:easy,moderate,hard,strenuous'],
            'is_active' => ['nullable', 'boolean'],
            'closed_since' => ['nullable', 'date'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        // An open mountain has no closure date.
        if ($data['is_active']) {
            $data['closed_since'] = null;
        }

        return $data;
    }
}
