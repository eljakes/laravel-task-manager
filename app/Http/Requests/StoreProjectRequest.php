<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    /**
     * Allow project creation because this assignment has no authentication.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validate data used to create a project.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:projects,name',
            ],
        ];
    }

    /**
     * Return user-friendly validation messages.
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
