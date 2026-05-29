<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BasecampResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'mountain_id' => $this->mountain_id,
            'name' => $this->name,
            'mountain' => $this->whenLoaded('mountain', fn () => [
                'id' => $this->mountain->id,
                'name' => $this->mountain->name,
            ]),
        ];
    }
}
