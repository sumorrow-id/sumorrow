<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\V1\BasecampController;
use App\Http\Controllers\Api\V1\MountainController;
use App\Http\Controllers\Api\V1\ProvinceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/v1/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (! Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['message' => 'Invalid credentials.', 'status' => 401], 401);
    }

    $token = $request->user()->createToken('api-token')->plainTextToken;

    return response()->json(['token' => $token, 'status' => 200]);
});

Route::prefix('v1')
    ->middleware(['auth:sanctum', 'throttle:api'])
    ->group(function () {
        // Auth context
        Route::get('/user', [UserController::class, 'show']);
        Route::get('/check-verification', [UserController::class, 'checkVerification']);

        // Provinces
        Route::get('/provinces', [ProvinceController::class, 'index']);
        Route::get('/provinces/{province}', [ProvinceController::class, 'show']);
        Route::get('/provinces/{province}/mountains', [ProvinceController::class, 'mountains']);

        // Mountains
        Route::get('/mountains', [MountainController::class, 'index']);
        Route::get('/mountains/{mountain}', [MountainController::class, 'show']);
        Route::get('/mountains/{mountain}/images', [MountainController::class, 'images']);
        Route::get('/mountains/{mountain}/basecamps', [MountainController::class, 'basecamps']);
        Route::get('/mountains/{mountain}/ratings', [MountainController::class, 'ratings']);

        // Basecamps
        Route::get('/basecamps/{basecamp}', [BasecampController::class, 'show']);
    });

Route::fallback(function (Request $request) {
    return response()->json([
        'message' => 'Endpoint not found.',
        'status' => 404,
        'available_root' => url('/api/v1'),
        'docs' => url('/docs/architecture/api-endpoint/README.md'),
    ], 404);
});
