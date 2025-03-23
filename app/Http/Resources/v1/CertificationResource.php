<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $image
 * @property string|null $provider
 * @property string|null $validity_period
 * @property string|null $level
 * @property string|null $benefits
 * @property string|null $link
 * @property string|null $prerequisites
 * @property string|null $skills
 * @property string|null $best_for
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Formation|null $formation
 */
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
            "slug" => $this->slug,
            "description" => $this->description,
            "image" => $this->image,
            "provider" => $this->provider,
            "validity_period" => $this->validity_period,
            "level" => $this->level,
            "benefits" => $this->benefits,
            "link" => $this->link,
            "formation" => $this->whenLoaded('formation', function () {
                return [
                    'id' => $this->formation?->id,
                    'name' => $this->formation?->name
                ];
            }),
            "prerequisites" => $this->prerequisites,
            "skills" => $this->skills,
            "best_for" => $this->best_for,
            "created_at" => $this->created_at->format('Y-m-d H:i'),
            "updated_at" => $this->updated_at->format('Y-m-d H:i'),
        ];
    }
}
