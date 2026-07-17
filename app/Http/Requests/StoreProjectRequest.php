<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    /**
     * Allow visitors to submit the project form.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for creating a project.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:projects,name'],
        ];
    }

    /**
     * Provide clearer validation feedback to the user.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter a project name.',
            'name.unique' => 'A project with this name already exists.',
        ];
    }
}
