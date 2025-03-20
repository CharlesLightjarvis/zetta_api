<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormationResource extends JsonResource
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
            "level" => $this->level,
            "duration" => $this->duration,
            "price" => $this->price,
            "capacity" => $this->capacity,
            "enrolled_students" => $this->enrolled_students,
            "teacher" => new UserResource($this->teacher),
            "category" => new CategoryResource($this->category),
            "course_type" => $this->course_type,
            "start_date" => $this->start_date->format('Y-m-d'),
            "end_date" => $this->end_date->format('Y-m-d'),
            "link" => $this->link,
            "prerequisites" => $this->prerequisites,
            "objectives" => $this->objectives,
            "certifications" => $this->whenLoaded('certifications', function () {
                return $this->certifications->map(function ($certification) {
                    return [
                        'id' => $certification->id,
                        'name' => $certification->name
                    ];
                });
            }),
            "modules" => $this->whenLoaded('modules', function () {
                return $this->modules->map(function ($module) {
                    return [
                        'id' => $module->id,
                        'name' => $module->name,
                        'description' => $module->description,
                        'lessons' => $module->lessons->map(function ($lesson) {
                            return [
                                'id' => $lesson->id,
                                'name' => $lesson->name
                            ];
                        })
                    ];
                });
            }),
            "created_at" => $this->created_at->format('Y-m-d H:i'),
            "updated_at" => $this->updated_at->format('Y-m-d H:i'),
        ];
    }
}
