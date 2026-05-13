<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BasecampResource;
use App\Models\Basecamp;

class BasecampController extends Controller
{
    public function show(Basecamp $basecamp)
    {
        $basecamp->load('mountain');

        return BasecampResource::make($basecamp);
    }
}
