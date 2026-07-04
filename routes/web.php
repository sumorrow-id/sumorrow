<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\GearController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePostController;
use App\Http\Controllers\WeatherController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
--------------------------------------------------------------------------
User-facing routes
--------------------------------------------------------------------------
Everything in this group is off-limits to admins: `redirect.admin` sends
any authenticated admin to the admin dashboard instead.
*/
Route::middleware('redirect.admin')->group(function () {
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
    Route::middleware('throttle:weather')->group(function () {
        Route::get('/weather/{mountain}', [WeatherController::class, 'show'])->name('weather.show');
        Route::get('/weather/{mountain}/forecast', [WeatherController::class, 'forecast'])->name('weather.forecast');
    });

    /*
    --------------------------------------------------------------------------
    Community Forum Tab Routes
    --------------------------------------------------------------------------
    */
    // Main forum feed (supports ?tag= query filter)
    Route::get('/community/forum', [PostController::class, 'index'])->name('community.forum');

    // Post detail / thread view (public — guests can read)
    Route::get('/community/posts/{post}', [PostController::class, 'show'])->name('community.posts.show');

    Route::get('/api/docs', function () {
        if (! Auth::check()) {
            return redirect('/home')->with('warning', __('common.api_docs_login_required'));
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
        Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:login');

        // Register
        Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

        // Google OAuth
        Route::get('/auth/google/redirect', [LoginController::class, 'redirectToGoogle'])->name('google.redirect');
        Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

        // Forgot / Reset Password
        Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
        Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
    });

    /*
    --------------------------------------------------------------------------
    Authenticated Routes (Logged In Users Only)
    --------------------------------------------------------------------------
    */
    Route::middleware('auth')->group(function () {
        // Profile
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // MyCommunity
        Route::post('/community/{community}/join', [CommunityController::class, 'join'])->name('community.join');
        Route::post('/community/create', [CommunityController::class, 'store'])->name('community.store');
        Route::post('/community/{community}/leave', [CommunityController::class, 'leave'])->name('community.leave');
        Route::get('/community/{community}', [CommunityController::class, 'show'])->name('community.show');
        Route::patch('/community/{community}/update-image', [CommunityController::class, 'updateImage'])->name('community.update-image');

        // Event Khusus MyCommunity (Create & Delete Event)
        Route::post('/community/{community}/events', [EventController::class, 'store'])->name('community.events.store');
        Route::delete('/community/events/{event}', [EventController::class, 'destroy'])->name('community.events.destroy');

        // Gear
        Route::post('/gears', [GearController::class, 'store'])->name('gears.store');
        Route::put('/gears/{gear}', [GearController::class, 'update'])->name('gears.update');
        Route::delete('/gears/{gear}', [GearController::class, 'destroy'])->name('gears.destroy');

        // Explore / Mountain
        Route::post('/explore/{id}/ratings', [ExploreController::class, 'storeRating'])->name('explore.ratings.store');

        // Posts (Profile)
        Route::get('/profile/posts', [ProfilePostController::class, 'index'])->name('profile.posts.index');
        Route::get('/profile/posts/create', [ProfilePostController::class, 'create'])->name('profile.posts.create');
        Route::post('/profile/posts', [ProfilePostController::class, 'store'])->name('profile.posts.store');
        Route::get('/profile/posts/{post}', [ProfilePostController::class, 'show'])->name('profile.posts.show');

        // Community Forum — Store a new quick post from the feed composer
        Route::post('/community/posts', [PostController::class, 'store'])->name('community.posts.store');

        // Community Forum — Store a comment on a specific post
        Route::post('/community/posts/{post}/comments', [PostController::class, 'storeComment'])->name('community.posts.comments.store');

        // Community Forum — Toggle like on a post
        Route::post('/community/posts/{post}/like', [PostController::class, 'toggleLike'])->name('community.posts.like');

        // Community Forum — Toggle save/bookmark on a post
        Route::post('/community/posts/{post}/save', [PostController::class, 'toggleSave'])->name('community.posts.save');

        // Delete an own post (forum post, community post, or summit log)
        Route::delete('/community/posts/{post}', [PostController::class, 'destroy'])->name('community.posts.destroy');

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

                return back()->with('message', __('auth.verify_email_resent_message'));
            })->middleware('throttle:6,1')->name('verification.send');

        });
    });
});

/*
--------------------------------------------------------------------------
Routes shared by users and admins
--------------------------------------------------------------------------
*/
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

/*
--------------------------------------------------------------------------
Admin Routes
--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // User management
    Route::get('/user-updates', [AdminController::class, 'userUpdates'])->name('admin.user-updates');
    Route::get('/user-updates/export', [AdminController::class, 'exportUsers'])->name('admin.users.export');
    Route::patch('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('admin.users.role');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');

    // Mountain management
    Route::get('/mountain-data', [AdminController::class, 'mountainData'])->name('admin.mountain-data');
    Route::get('/mountains/create', [AdminController::class, 'createMountain'])->name('admin.mountains.create');
    Route::post('/mountains', [AdminController::class, 'storeMountain'])->name('admin.mountains.store');
    Route::get('/mountains/{mountain}/edit', [AdminController::class, 'editMountain'])->name('admin.mountains.edit');
    Route::put('/mountains/{mountain}', [AdminController::class, 'updateMountain'])->name('admin.mountains.update');
    Route::delete('/mountains/{mountain}', [AdminController::class, 'destroyMountain'])->name('admin.mountains.destroy');
});
