<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function store(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->safe()->only(['email', 'password']))) {
            return response()->json(['message' => 'Invalid credentials.', 'status' => 401], 401);
        }

        $token = $request->user()->createToken('api-token')->plainTextToken;

        return response()->json(['token' => $token, 'status' => 200]);
    }
}
