<?php

namespace App\Http\Resources\v1;

use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'fullName' => $this->fullName,
            'email' => $this->email,
            'status' => $this->status,
            'imageUrl' => $this->imageUrl,
            'role' => $this->roles()->pluck('name')->first(),
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i'),
        ];

        if ($this->roles()->pluck('name')->first() === RoleEnum::TEACHER->value) {
            $data['bio'] = $this->bio;
            $data['title'] = $this->title;
        }

        return $data;
    }
}
