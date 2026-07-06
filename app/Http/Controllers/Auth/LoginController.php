<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\GoogleAuthService;
use Exception;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginController extends Controller
{
    public function __construct(
        protected GoogleAuthService $socialAuth,
        protected AuthManager $auth,
        protected Hasher $hasher,
        protected SocialiteFactory $socialite,
    ) {}

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
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

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = $this->socialite->driver('google')->user();
            $user = $this->socialAuth->findOrCreateUser($googleUser);
            $this->auth->login($user, true);
            request()->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('home');
        } catch (Exception $e) {
            report($e);

            return redirect('/login')->with('error', __('auth.google_login_failed'));
        }
    }

    public function redirectToGoogle(): RedirectResponse
    {
        return $this->socialite->driver('google')->redirect();
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->auth->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
