<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    public function __construct(protected Hasher $hasher) {}

    public function showResetForm(Request $request, ?string $token = null): View
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function reset(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password_hash' => Hash::make($password),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect('/login')->with('success', __('auth.password_reset_success'));
        }

        return back()->withErrors(['email' => __('auth.password_reset_invalid')]);
    }
}
