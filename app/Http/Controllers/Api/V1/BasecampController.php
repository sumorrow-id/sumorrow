<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BasecampResource;
use App\Models\Basecamp;
use Illuminate\Http\Resources\Json\JsonResource;

class BasecampController extends Controller
{
    public function show(Basecamp $basecamp): JsonResource
    {
        $basecamp->load('mountain');

        return BasecampResource::make($basecamp);
    }
}
