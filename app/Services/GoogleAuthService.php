<?php

namespace App\Services;

use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class GoogleAuthService
{
    public function findOrCreateUser(SocialiteUser $socialUser): User
    {
        $user = User::query()->where('google_id', $socialUser->id)->orWhere('email', $socialUser->email)->first();

        if (! $user) {
            return User::create([
                'username' => $socialUser->name,
                'email' => $socialUser->email,
                'google_id' => $socialUser->id,
                'email_verified_at' => now(),
                'password_hash' => null,
                'avatar_url' => $socialUser->getAvatar(),
            ]);
        }

        $user->update([
            'google_id' => $socialUser->id,
            'avatar_url' => $socialUser->getAvatar(),
            'email_verified_at' => $user->email_verified_at ?? now(),
        ]);

        return $user;
    }
}
