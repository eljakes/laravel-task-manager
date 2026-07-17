<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ReorderTasksRequest extends FormRequest
{
    /**
     * Allow task reordering because this assignment has no authentication.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validate the drag-and-drop task order payload.
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
            'tasks' => [
                'required',
                'array',
                'min:1',
            ],
            'tasks.*' => [
                'integer',
                'distinct',
                'exists:tasks,id',
            ],
        ];
    }

    /**
     * Ensure the submitted IDs exactly match the selected project's tasks.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $submittedTaskIds = collect($this->input('tasks'))
                ->map(fn (mixed $taskId): int => (int) $taskId)
                ->sort()
                ->values();

            $projectTaskIds = Task::query()
                ->where('project_id', $this->integer('project_id'))
                ->pluck('id')
                ->sort()
                ->values();

            if ($submittedTaskIds->all() !== $projectTaskIds->all()) {
                $validator->errors()->add(
                    'tasks',
                    'The submitted task order does not match the selected project.'
                );
            }
        });
    }
}
