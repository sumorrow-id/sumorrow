<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRoleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function index(Request $request): View
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

    public function update(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', __('admin.cannot_change_own_role'));
        }

        $user->role = $request->validated('role');
        $user->save();

        return back()->with('success', __('admin.role_updated', ['username' => $user->username, 'role' => $user->role]));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', __('admin.cannot_delete_own_account'));
        }

        $user->delete();

        return back()->with('success', __('admin.user_deleted', ['username' => $user->username]));
    }

    public function export(): StreamedResponse
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
}
