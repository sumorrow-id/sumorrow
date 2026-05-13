<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MountainImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'mountain_id' => $this->mountain_id,
            'image_url'   => $this->image_url,
            'source_url'  => $this->source_url,
            'position'    => $this->position,
            'is_cover'    => (bool) $this->is_cover,
            'uploaded_at' => optional($this->uploaded_at)->toIso8601String(),
        ];
    }
}
