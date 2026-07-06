<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(Request $request): User
    {
        return $request->user();
    }

    public function checkVerification(Request $request): JsonResponse
    {
        return response()->json([
            'verified' => $request->user() && $request->user()->hasVerifiedEmail(),
        ]);
    }
}
