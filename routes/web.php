<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ExploreController;

Route::get('/', [HomeController::class,'index'])->name('home');
Route::get('/explore',[ExploreController::class,'dummy'])->name('explore');
