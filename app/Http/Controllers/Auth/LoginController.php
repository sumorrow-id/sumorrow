<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\SocialAuthInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class LoginController extends Controller
{
    public function __construct(
        protected SocialAuthInterface $socialAuth
    ) {}

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember');
        $user = User::query()->where('email', $request->email)->first();

        // Cek apakah user ada DAN passwordnya cocok
        if ($user && Hash::check($request->password, $user->password_hash)) {
            Auth::login($user, $remember);
            $request->session()->regenerate();
            return redirect()->route('home');
        }

        return back()->withErrors(['email' => 'Email or password incorrect.'])->onlyInput('email');
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = $this->socialAuth->findOrCreateUser($googleUser);
            Auth::login($user, true);
            return redirect()->route('home');
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Gagal login menggunakan Google: ' . $e->getMessage());
        }
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
