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
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember');
        $user = User::where('email', $request->email)->first();

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

            // Cari user berdasarkan email
            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if (!$user) {
                // Jika user belum ada di DB Sumorrow, buat baru sesuai ERD
                $user = User::create([
                    // 'id' => (string) Str::uuid(), 
                    'username' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'email_verified_at' => now(),
                    'password_hash' => null,
                    'avatar_url' => $googleUser->getAvatar(),
                ]);
            } else {
                // Jika sudah ada
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar_url' => $googleUser->getAvatar(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            }

            Auth::login($user, true);
            return redirect()->route('home');

        } catch (\Exception $e) {
            echo "<h1>Error Terdeteksi:</h1>";
            echo "<p>" . $e->getMessage() . "</p>";
            dd($e);
            // Jika ada error (misal koneksi atau user cancel)
            // return redirect('/login')->with('error', 'Gagal login menggunakan Google: '. $e->getMessage());
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
