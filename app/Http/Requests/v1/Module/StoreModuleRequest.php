<?php

namespace App\Http\Requests\v1\Module;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuleRequest extends FormRequest
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
            'formation_ids' => 'nullable|array',
            'formation_ids.*' => 'required|uuid|exists:formations,id',
            'lessons' => 'nullable|array',
            'lessons.*.name' => 'required|string|max:255',
            'lessons.*.description' => 'nullable|string|max:1000',
        ];
    }
}
