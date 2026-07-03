<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\User;
use App\Services\AchievementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index(AchievementService $achievementService)
    {
        /** @var User $user */
        $user = Auth::user();

        // Cek dan unlock achievement baru jika memenuhi kriteria
        $achievementService->checkAndUnlockAchievements($user);

        // 1. Ambil data kontribusi user.
        // Post forum dibedakan dari catatan puncak lewat category tags:
        // hanya post forum yang memiliki baris di post_tags.
        $forumPosts = $user->posts()
            ->whereHas('tags')
            ->with(['tags', 'images' => function ($query) {
                $query->orderBy('position', 'asc');
            }])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $hikingPosts = $user->posts()
            ->whereDoesntHave('tags')
            ->with(['mountain.province', 'images' => function ($query) {
                $query->orderBy('position', 'asc');
            }])
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();

        $gears = $user->gears()->orderBy('category')->get();

        $allAchievements = Achievement::all();
        $userAchievements = $user->achievements()->get()->keyBy('id');

        // 2. Ambil ulasan gunung terakhir milik user untuk sidebar
        $lastReviews = $user->ratings()
            ->with(['mountain.province', 'mountain.images'])
            ->latest('created_at')
            ->take(5)
            ->get();

        // 3. Kirim semua variabel ke view profile
        return view('profile.profile', compact('forumPosts', 'hikingPosts', 'gears', 'allAchievements', 'userAchievements', 'user', 'lastReviews'));
    }

    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar_url && ! str_contains($user->avatar_url, 'http')) {
                Storage::disk('public')->delete($user->avatar_url);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = $avatarPath;
        }

        if ($request->hasFile('cover')) {
            if ($user->cover_url && ! str_contains($user->cover_url, 'http')) {
                Storage::disk('public')->delete($user->cover_url);
            }
            $coverPath = $request->file('cover')->store('covers', 'public');
            $user->cover_url = $coverPath;
        }

        $user->username = $request->username;
        $user->bio = $request->bio;
        $user->save();

        return redirect()->route('profile')->with('success', __('profile.updated_successfully'));
    }
}
