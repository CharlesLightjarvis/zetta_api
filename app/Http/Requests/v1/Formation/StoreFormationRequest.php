<?php

namespace App\Http\Requests\v1\Formation;

use App\Enums\CourseTypeEnum;
use App\Enums\LevelEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFormationRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|string|max:255',
            'level' => ['required', Rule::enum(LevelEnum::class)],
            'duration' => 'required|integer|min:1',
            'price' => 'required|integer|min:0',
            'capacity' => 'required|integer|min:1',
            'teacher_id' => 'required|uuid|exists:users,id',
            'category_id' => 'required|uuid|exists:categories,id',
            'course_type' => ['required', Rule::enum(CourseTypeEnum::class)],
            'end_date' => 'required|date',
            'start_date' => 'required|date',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'nullable|string',
            'objectives' => 'nullable|array',
            'objectives.*' => 'nullable|string',
        ];
    }
}
