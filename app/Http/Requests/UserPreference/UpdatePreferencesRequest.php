<?php

namespace App\Http\Requests\UserPreference;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePreferencesRequest extends FormRequest
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
            'sources' => ['nullable', 'array'],
            'sources.*' => ['integer', 'exists:sources,id'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'authors' => ['nullable', 'array'],
            'authors.*' => ['string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'sources.*.exists' => 'One or more selected sources do not exist.',
            'categories.*.exists' => 'One or more selected categories do not exist.',
        ];
    }
}
