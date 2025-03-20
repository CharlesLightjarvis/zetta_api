<?php

namespace App\Http\Requests\v1\Certification;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCertificationRequest extends FormRequest
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
            'provider' => 'sometimes|required|string|max:255',
            'validity_period' => 'sometimes|required|integer|min:1',
            'formation_id' => 'sometimes|required|uuid|exists:formations,id',
            'level' => 'sometimes|required|string|max:255',
            'benefits' => 'sometimes|nullable|array',
            'link' => 'sometimes|nullable|string|max:255',
        ];
    }
}
