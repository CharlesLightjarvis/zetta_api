<?php

namespace App\Http\Resources\v1;

use App\Models\Formation;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Module $resource
 */
class ModuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->resource->id,
            "name" => $this->resource->name,
            "slug" => $this->resource->slug,
            "description" => $this->resource->description,
            "formation" => $this->whenLoaded('formation', fn(): array => $this->getFormationData()),
            "lessons" => $this->whenLoaded('lessons', fn(): array => $this->getLessonsData()),
            "created_at" => $this->resource->created_at->format('Y-m-d H:i'),
            "updated_at" => $this->resource->updated_at->format('Y-m-d H:i'),
        ];
    }

    /**
     * Get formatted formation data
     */
    private function getFormationData(): array
    {
        /** @var Formation $formation */
        $formation = $this->resource->formation;

        return [
            'id' => $formation->id,
            'name' => $formation->name
        ];
    }

    /**
     * Get formatted lessons data
     */
    private function getLessonsData(): array
    {
        return $this->resource->lessons->map(function (Model $model, int $key): array {
            /** @var Lesson $model */
            return [
                'id' => $model->id,
                'name' => $model->name
            ];
        })->toArray();
    }
}
