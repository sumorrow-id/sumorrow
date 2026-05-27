<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MountainDetailResource extends JsonResource
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
            'length_km' => $this->length_km,
            'elevation_gain_m' => $this->elevation_gain_m,
            'coordinates' => $this->coordinates,
            'description' => $this->description,
            'is_active' => (bool) $this->is_active,
            'closed_since' => optional($this->closed_since)->toDateString(),
            'avg_rating' => (float) $this->avg_rating,
            'cover_image' => $cover,
            'counts' => [
                'images' => $this->whenCounted('images'),
                'basecamps' => $this->whenCounted('basecamps'),
                'ratings' => $this->whenCounted('ratings'),
            ],
        ];
    }
}
