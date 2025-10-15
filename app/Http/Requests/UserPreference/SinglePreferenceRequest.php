<?php

namespace App\Http\Requests\UserPreference;

use Illuminate\Foundation\Http\FormRequest;

class SinglePreferenceRequest extends FormRequest
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
            'source_id' => ['nullable', 'integer', 'exists:sources,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'author_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
