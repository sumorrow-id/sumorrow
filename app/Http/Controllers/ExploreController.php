<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Mountain;
use App\Models\Province;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function index()
    {
        $mountains = Mountain::with('images')->inRandomOrder()->get();
        // Fetch 4 random provinces for the region filter
        $provinces = Province::inRandomOrder()->limit(4)->get();

        return view('explore', compact('mountains', 'provinces'));
    }
}
