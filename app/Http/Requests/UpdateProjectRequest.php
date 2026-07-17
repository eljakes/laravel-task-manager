<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Allow project updates because this assignment has no authentication.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validate data used to update a project.
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

                // Allow the current project to keep its existing name.
                Rule::unique('projects', 'name')
                    ->ignore($this->route('project')),
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
