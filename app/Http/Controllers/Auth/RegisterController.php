<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(
        protected AuthManager $auth,
        protected Hasher $hasher,
    ) {}

    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $words = explode(' ', trim($request->username));
        $initials = strtoupper(substr($words[0], 0, 1));
        if (count($words) > 1) {
            $initials .= strtoupper(substr($words[1], 0, 1));
        }
        $avatarUrl = 'https://ui-avatars.com/api/?name='.urlencode($initials).'&background=random';

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password_hash' => $this->hasher->make($request->password),
            'avatar_url' => $avatarUrl,
        ]);

        // The Registered event triggers SendEmailVerificationNotification — do not also send manually.
        event(new Registered($user));
        $this->auth->login($user);

        return redirect()->route('verification.notice');
    }
}
