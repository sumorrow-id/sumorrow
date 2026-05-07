<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Kita harus mapping manual karena nama kolom password di db berbeda
        if (Auth::attempt([
            'email' => $credentials['email'], 
            'password' => $credentials['password'] 
        ])) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors(['email' => 'Email atau password incorrect.']);
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate([
                'email' => $googleUser->email,
            ], [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'username' => $googleUser->name, // Masuk ke username
                'password_hash' => Hash::make(Str::random(16)), // Masuk ke password_hash
                'avatar_url' => $googleUser->getAvatar(), // Sesuai kolom di db kamu
            ]);

            Auth::login($user);
            return redirect()->intended('dashboard');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Gagal login Google.');
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
