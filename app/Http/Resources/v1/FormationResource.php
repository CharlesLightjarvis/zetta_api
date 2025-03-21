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
            "category" => new CategoryResource($this->category),
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
            "sessions" => $this->whenLoaded('sessions', function () {
                return $this->sessions
                    ->sortByDesc(function ($session) {
                        return $session->start_date->diffInSeconds(now());
                    })
                    ->values() // Ceci va réindexer le tableau séquentiellement
                    ->map(function ($session) {
                        return [
                            'id' => $session->id,
                            'course_type' => $session->course_type,
                            'start_date' => $session->start_date->format('Y-m-d'),
                            'end_date' => $session->end_date->format('Y-m-d'),
                            'capacity' => $session->capacity,
                            'enrolled_students' => $session->enrolled_students,
                            'teacher' => $session->teacher ? new UserResource($session->teacher) : null
                        ];
                    });
            }),
            "created_at" => $this->created_at->format('Y-m-d H:i'),
            "updated_at" => $this->updated_at->format('Y-m-d H:i'),
        ];
    }
}
