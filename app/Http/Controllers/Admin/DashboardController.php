<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mountain;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalUsers = User::count();
        $newUsersCount = User::where('created_at', '>=', now()->subDays(7))->count();
        $totalMountains = Mountain::count();
        $activeMountains = Mountain::where('is_active', true)->count();
        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact('totalUsers', 'newUsersCount', 'totalMountains', 'activeMountains', 'recentUsers'));
    }
}
