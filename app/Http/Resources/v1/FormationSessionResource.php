<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormationSessionResource extends JsonResource
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
            "course_type" => $this->course_type,
            "start_date" => $this->start_date->format('Y-m-d'),
            "end_date" => $this->end_date->format('Y-m-d'),
            "capacity" => $this->capacity,
            "enrolled_students" => $this->enrolled_students,
            "formation" => $this->whenLoaded('formation', function () {
                return [
                    'id' => $this->formation->id,
                    'name' => $this->formation->name
                ];
            }),
            "teacher" => $this->whenLoaded('teacher', function () {
                return new UserResource($this->teacher);
            }),
            "created_at" => $this->created_at->format('Y-m-d H:i'),
            "updated_at" => $this->updated_at->format('Y-m-d H:i'),
        ];
    }
}
