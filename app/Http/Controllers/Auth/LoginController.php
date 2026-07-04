<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\SocialAuthInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;

class LoginController extends Controller
{
    public function __construct(
        protected SocialAuthInterface $socialAuth,
        protected AuthManager $auth,
        protected Hasher $hasher,
        protected SocialiteFactory $socialite,
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

        if ($user && $this->hasher->check($request->password, $user->password_hash)) {
            $this->auth->login($user, $remember);
            $request->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('home');
        }

        return back()->withErrors(['email' => __('auth.invalid_credentials')])->onlyInput('email');
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = $this->socialite->driver('google')->user();
            $user = $this->socialAuth->findOrCreateUser($googleUser);
            $this->auth->login($user, true);

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('home');
        } catch (Exception $e) {
            report($e);

            return redirect('/login')->with('error', __('auth.google_login_failed'));
        }
    }

    public function redirectToGoogle()
    {
        return $this->socialite->driver('google')->redirect();
    }

    public function logout(Request $request)
    {
        $this->auth->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
