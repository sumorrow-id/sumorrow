<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __construct(
        protected AuthManager $auth,
        protected Hasher $hasher,
    ) {}

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password_hash' => $this->hasher->make($request->password),
        ]);

        event(new Registered($user));
        $user->sendEmailVerificationNotification();
        $this->auth->login($user);

        return redirect()->route('verification.notice');
    }
}
