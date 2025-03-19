<?php

namespace App\Http\Requests\v1\Certification;

use Illuminate\Foundation\Http\FormRequest;

class StoreCertificationRequest extends FormRequest
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
            'provider' => 'required|string|max:255',
            'validity_period' => 'required|integer|min:1',
            'formation_id' => 'required|uuid|exists:formations,id',
            'level' => 'required|string|max:255',
            'benefits' => 'nullable|array',
            'link' => 'nullable|string|max:255',
        ];
    }
}
