<?php

namespace App\Http\Requests\v1\Formation;

use App\Enums\CourseTypeEnum;
use App\Enums\LevelEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string|max:1000',
            'image' => 'sometimes|nullable|string|max:255',
            'level' => ['sometimes', Rule::enum(LevelEnum::class)],
            'duration' => 'sometimes|integer|min:1',
            'price' => 'sometimes|integer|min:0',
            'category_id' => 'sometimes|required|uuid|exists:categories,id',
            'prerequisites' => 'sometimes|nullable|array',
            'prerequisites.*' => 'sometimes|nullable|string',
            'objectives' => 'sometimes|nullable|array',
            'objectives.*' => 'sometimes|nullable|string',
        ];
    }
}
