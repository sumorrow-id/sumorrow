<?php

namespace App\Services;

use App\Contracts\SocialAuthInterface;
use App\Models\User;
use Override;

class GoogleAuthService implements SocialAuthInterface
{
    /**
     * @param  \Laravel\Socialite\Contracts\User  $socialUser
     */
    #[Override]
    public function findOrCreateUser($socialUser): User
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
