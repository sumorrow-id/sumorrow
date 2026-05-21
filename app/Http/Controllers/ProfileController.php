<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index(){
        $user = Auth::user();
        
        $posts = $user->posts()
            ->with(['mountain', 'images' => function ($query) {
                $query->orderBy('position', 'asc');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        $gears = $user->gears()->orderBy('category')->get();
        $achievements = $user->achievements()->orderByPivot('unlocked_at', 'desc')->get();

        return view('profile.profile', compact('posts', 'gears', 'achievements', 'user'));
    }

    public function edit() {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request) {
        $user = Auth::user();

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar_url && !str_contains($user->avatar_url, 'http')) {
                Storage::disk('public')->delete($user->avatar_url);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = $avatarPath;
        }

        if ($request->hasFile('cover')) {
            if ($user->cover_url && !str_contains($user->cover_url, 'http')) {
                Storage::disk('public')->delete($user->cover_url);
            }
            $coverPath = $request->file('cover')->store('covers', 'public');
            $user->cover_url = $coverPath;
        }

        $user->username = $request->username;
        $user->bio = $request->bio;
        $user->save();

        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }
}
