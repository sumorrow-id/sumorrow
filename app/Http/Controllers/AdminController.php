<?php

namespace App\Http\Controllers;

use App\Models\Mountain;
use App\Models\Post;
use App\Models\PostReply;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();
        $newUsersCount = User::where('created_at', '>=', now()->subDays(7))->count();
        $forumPostsCount = Post::count();
        $recentPosts = Post::with('author')->latest()->take(5)->get();

        return view('admin.dashboard', compact('recentUsers', 'newUsersCount', 'forumPostsCount', 'recentPosts'));
    }

    public function forumModeration()
    {
        $totalPosts = Post::count();
        $postsToday = Post::whereDate('created_at', now()->toDateString())->count();
        $totalReplies = PostReply::count();
        $recentPosts = Post::with('author')->latest()->take(10)->get();

        return view('admin.forum-moderation', compact('totalPosts', 'postsToday', 'totalReplies', 'recentPosts'));
    }

    public function userUpdates()
    {
        $totalUsers = User::count();
        $newThisWeek = User::where('created_at', '>=', now()->subDays(7))->count();
        $newThisMonth = User::where('created_at', '>=', now()->startOfMonth())->count();
        $recentUsers = User::latest()->take(12)->get();

        return view('admin.user-updates', compact('totalUsers', 'newThisWeek', 'newThisMonth', 'recentUsers'));
    }

    public function mountainData()
    {
        $totalMountains = Mountain::count();
        $activeMountains = Mountain::where('is_active', true)->count();
        $challengingRoutes = Mountain::whereIn('difficulty', ['hard', 'strenuous'])->count();
        $mountains = Mountain::with('province')->latest('id')->take(12)->get();

        return view('admin.mountain-data', compact('totalMountains', 'activeMountains', 'challengingRoutes', 'mountains'));
    }
}
