<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MountainRatingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'score'      => (int) $this->score,
            'review'     => $this->review,
            'user'       => $this->whenLoaded('user', fn () => [
                'username'   => $this->user->username,
                'avatar_url' => $this->user->avatar_url,
            ]),
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
