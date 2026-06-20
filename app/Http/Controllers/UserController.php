<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Toggle follow/unfollow for a given user.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleFollow(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'You cannot follow yourself.'], 400);
        }

        $user->followers()->toggle(auth()->id());

        return response()->json([
            'is_following' => $user->followers()->where('follower_id', auth()->id())->exists()
        ]);
    }
}
