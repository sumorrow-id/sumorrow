<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\GearController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
--------------------------------------------------------------------------
Public Routes
--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'redirectToHome'])->name('root');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/explore', [ExploreController::class, 'index'])->name('explore');
Route::get('/explore/{id}', [ExploreController::class, 'show'])->name('explore.show');
Route::get('/community', [CommunityController::class, 'index'])->name('community');

Route::get('/api/docs', function () {
    if (! Auth::check()) {
        return redirect('/home')->with('warning', 'You need to register or log in first to access the API documentation.');
    }

    return view('api.docs');
})->name('api.docs');

/*
--------------------------------------------------------------------------
Guest Routes (Unauthenticated Users Only)
--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('showLogin');
    Route::post('/login', [LoginController::class, 'login']);

    // Register
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

    // Google OAuth
    Route::get('/auth/google/redirect', [LoginController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback']);
});

/*
--------------------------------------------------------------------------
Authenticated Routes (Logged In Users Only)
--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Gear
    Route::post('/gears', [GearController::class, 'store'])->name('gears.store');
    Route::put('/gears/{gear}', [GearController::class, 'update'])->name('gears.update');
    Route::delete('/gears/{gear}', [GearController::class, 'destroy'])->name('gears.destroy');

    // Explore / Mountain
    Route::post('/explore/{id}/ratings', [ExploreController::class, 'storeRating'])->name('explore.ratings.store');

    // Posts
    Route::get('/profile/posts', [\App\Http\Controllers\ProfilePostController::class, 'index'])->name('profile.posts.index');
    Route::get('/profile/posts/create', [\App\Http\Controllers\ProfilePostController::class, 'create'])->name('profile.posts.create');
    Route::post('/profile/posts', [\App\Http\Controllers\ProfilePostController::class, 'store'])->name('profile.posts.store');
    Route::get('/profile/posts/{post}', [\App\Http\Controllers\ProfilePostController::class, 'show'])->name('profile.posts.show');

    // Email Verification Configuration
    Route::prefix('email')->group(function () {
        Route::get('/verify', function () {
            return view('auth.verify-email');
        })->name('verification.notice');

        Route::get('/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
            $request->fulfill();

            return redirect()->route('home')->with('verified', true);
        })->middleware('signed')->name('verification.verify');

        Route::post('/verification-notification', function (Request $request) {
            $request->user()->sendEmailVerificationNotification();

            return back()->with('message', 'Verification link sent!');
        })->middleware('throttle:6,1')->name('verification.send');
    });

    // Admin route
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/forum-moderation', [AdminController::class, 'forumModeration'])->name('admin.forum-moderation');
        Route::get('/user-updates', [AdminController::class, 'userUpdates'])->name('admin.user-updates');
        Route::get('/mountain-data', [AdminController::class, 'mountainData'])->name('admin.mountain-data');
    });
});
