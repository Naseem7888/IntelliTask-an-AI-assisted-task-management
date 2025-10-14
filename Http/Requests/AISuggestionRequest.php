<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AISuggestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'min:10', 'max:500'],
            'max_suggestions' => ['nullable', 'integer', 'min:1', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'A task description is required to generate suggestions.',
            'description.min' => 'The description must be at least :min characters long for effective suggestions.',
        ];
    }
}
