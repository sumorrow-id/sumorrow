<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', [HomeController::class,'index'])->name('home');
Route::get('/explore',[ExploreController::class,'dummy'])->name('explore');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [LoginController::class, 'login']);

Route::get('/auth/google/redirect', [LoginController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// ini utk pastiin user verified sblm akses dashboard
Route::get('/dashboard', function(){
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 1. Halaman pemberitahuan verifikasi (Notice)
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 2. Link verifikasi yang diklik di email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('dashboard'); // Sesuaikan mau redirect ke mana setelah sukses
})->middleware(['auth', 'signed'])->name('verification.verify');

// 3. Kirim ulang email verifikasi (Resend)
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Tambahkan ini di web.php
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');