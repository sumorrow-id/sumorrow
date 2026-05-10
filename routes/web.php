<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ExploreController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/explore', [ExploreController::class, 'index'])->name('explore');

// Admin route
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/forum-moderation', [AdminController::class, 'forumModeration'])->name('admin.forum-moderation');
    Route::get('/user-updates', [AdminController::class, 'userUpdates'])->name('admin.user-updates');
    Route::get('/mountain-data', [AdminController::class, 'mountainData'])->name('admin.mountain-data');
});