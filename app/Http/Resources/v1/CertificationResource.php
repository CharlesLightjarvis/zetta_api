<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "image" => $this->image,
            "provider" => $this->provider,
            "validity_period" => $this->validity_period,
            "level" => $this->level,
            "benefits" => $this->benefits,
            "link" => $this->link,
            "formation" => $this->whenLoaded('formation', function () {
                return [
                    'id' => $this->formation->id,
                    'name' => $this->formation->name
                ];
            }),
            "created_at" => $this->created_at->format('Y-m-d H:i'),
            "updated_at" => $this->updated_at->format('Y-m-d H:i'),
        ];
    }
}
