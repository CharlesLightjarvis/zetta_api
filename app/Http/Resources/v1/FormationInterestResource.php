<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $fullName
 * @property string $email
 * @property string|null $phone
 * @property string|null $message
 * @property string $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Formation|null $formation
 * @property-read \App\Models\Category|null $category
 */
class FormationInterestResource extends JsonResource
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
            "fullName" => $this->fullName,
            "email" => $this->email,
            "phone" => $this->phone,
            "message" => $this->message,
            "status" => $this->status,
            "formation" => $this->whenLoaded('formation', function () {
                return [
                    'id' => $this->formation?->id,
                    'slug' => $this->formation?->slug,
                    'image' => $this->formation?->image,
                    'description' => $this->formation?->description,
                    'duration' => $this->formation?->duration,
                    'level' => $this->formation?->level,
                    'price' => $this->formation?->price,
                    'name' => $this->formation?->name,
                    'category' => [
                        'id' => $this->formation?->category?->id,
                        'name' => $this->formation?->category?->name
                    ]
                ];
            }),
            "created_at" => $this->created_at->format('Y-m-d H:i'),
            "updated_at" => $this->updated_at->format('Y-m-d H:i'),
        ];
    }
}
