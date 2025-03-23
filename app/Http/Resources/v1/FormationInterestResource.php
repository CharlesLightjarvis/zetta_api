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
            "formation" => $this->whenLoaded('formation', function () {
                return [
                    'id' => $this->formation?->id,
                    'name' => $this->formation?->name
                ];
            }),
            "fullName" => $this->fullName,
            "email" => $this->email,
            "phone" => $this->phone,
            "message" => $this->message,
            "status" => $this->status,
            "created_at" => $this->created_at->format('Y-m-d H:i'),
            "updated_at" => $this->updated_at->format('Y-m-d H:i'),
        ];
    }
}
