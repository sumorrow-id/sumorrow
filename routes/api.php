<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/check-verification', function (Request $request) {
    return response()->json([
        'verified' => $request->user() && $request->user()->hasVerifiedEmail()
    ]);
})->middleware('auth:sanctum');
