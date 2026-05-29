<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MountainResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cover = $this->whenLoaded('images', function () {
            $cover = $this->images->firstWhere('is_cover', true) ?? $this->images->first();

            return $cover?->image_url;
        });

        return [
            'id' => $this->id,
            'name' => $this->name,
            'province' => $this->whenLoaded('province', fn () => [
                'id' => $this->province->id,
                'name' => $this->province->name,
            ]),
            'difficulty' => $this->difficulty,
            'elevation_masl' => $this->elevation_masl,
            'avg_rating' => (float) $this->avg_rating,
            'is_active' => (bool) $this->is_active,
            'cover_image' => $cover,
        ];
    }
}
