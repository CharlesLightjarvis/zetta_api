<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
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
            "formation" => $this->whenLoaded('formation', function () {
                return [
                    'id' => $this->formation->id,
                    'name' => $this->formation->name
                ];
            }),
            "lessons" => $this->whenLoaded('lessons', function () {
                return $this->lessons->map(function ($lesson) {
                    return [
                        'id' => $lesson->id,
                        'name' => $lesson->name
                    ];
                });
            }),
            "created_at" => $this->created_at->format('Y-m-d H:i'),
            "updated_at" => $this->updated_at->format('Y-m-d H:i'),
        ];
    }
}
