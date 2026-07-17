<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Allow task updates because this assignment has no authentication.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validate data used to update a task.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'project_id' => [
                'required',
                'integer',
                'exists:projects,id',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
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
            'project_id.required' => 'Please choose a project.',
            'project_id.exists' => 'Please choose a valid project.',
            'name.required' => 'Please enter a task name.',
        ];
    }
}
