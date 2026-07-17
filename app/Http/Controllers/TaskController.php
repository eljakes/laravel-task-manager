<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderTasksRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Display tasks for the selected project.
     */
    public function index(Request $request): View
    {
        $projects = Project::query()
            ->orderBy('name')
            ->get();

        $selectedProject = $this->selectedProject($projects, $request);

        $tasks = Task::query()
            ->with('project')
            ->when(
                $selectedProject,
                fn ($query) => $query->whereBelongsTo($selectedProject),
                fn ($query) => $query->whereRaw('1 = 0')
            )
            ->orderBy('priority')
            ->orderBy('created_at')
            ->get();

        return view('tasks.index', [
            'projects' => $projects,
            'selectedProject' => $selectedProject,
            'tasks' => $tasks,
        ]);
    }

    /**
     * Show the task creation form.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $projects = Project::query()
            ->orderBy('name')
            ->get();

        if ($projects->isEmpty()) {
            return redirect()
                ->route('projects.create')
                ->with('error', 'Create a project before adding tasks.');
        }

        $selectedProjectId = $projects
            ->firstWhere('id', $request->integer('project'))
            ?->id ?? $projects->first()->id;

        return view('tasks.create', [
            'projects' => $projects,
            'selectedProjectId' => $selectedProjectId,
        ]);
    }

    /**
     * Save a newly created task.
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $task = DB::transaction(function () use ($validated): Task {
            $project = Project::query()->findOrFail($validated['project_id']);

            return $project->tasks()->create([
                'name' => $validated['name'],
                'priority' => $this->nextPriorityForProject($project->id),
            ]);
        });

        return redirect()
            ->route('tasks.index', ['project' => $task->project_id])
            ->with('success', 'Task created successfully.');
    }

    /**
     * Show the task editing form.
     */
    public function edit(Task $task): View
    {
        $projects = Project::query()
            ->orderBy('name')
            ->get();

        return view('tasks.edit', [
            'projects' => $projects,
            'task' => $task,
        ]);
    }

    /**
     * Save changes to an existing task.
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $validated = $request->validated();
        $oldProjectId = $task->project_id;
        $newProjectId = (int) $validated['project_id'];

        DB::transaction(function () use (
            $task,
            $validated,
            $oldProjectId,
            $newProjectId
        ): void {
            $updates = [
                'project_id' => $newProjectId,
                'name' => $validated['name'],
            ];

            if ($oldProjectId !== $newProjectId) {
                $updates['priority'] = $this->nextPriorityForProject($newProjectId);
            }

            $task->update($updates);

            if ($oldProjectId !== $newProjectId) {
                $this->normalizePriorities($oldProjectId);
            }

            $this->normalizePriorities($newProjectId);
        });

        return redirect()
            ->route('tasks.index', ['project' => $newProjectId])
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Delete an existing task.
     */
    public function destroy(Task $task): RedirectResponse
    {
        $projectId = $task->project_id;

        DB::transaction(function () use ($task, $projectId): void {
            $task->delete();

            $this->normalizePriorities($projectId);
        });

        return redirect()
            ->route('tasks.index', ['project' => $projectId])
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Save the dragged task order and rewrite priorities from top to bottom.
     */
    public function reorder(ReorderTasksRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $projectId = (int) $validated['project_id'];

        DB::transaction(function () use ($validated, $projectId): void {
            foreach (array_values($validated['tasks']) as $index => $taskId) {
                Task::query()
                    ->whereKey($taskId)
                    ->where('project_id', $projectId)
                    ->update([
                        'priority' => $index + 1,
                    ]);
            }
        });

        return response()->json([
            'message' => 'Task order updated.',
        ]);
    }

    /**
     * Pick the requested project, or default to the first available project.
     *
     * @param  Collection<int, Project>  $projects
     */
    private function selectedProject(Collection $projects, Request $request): ?Project
    {
        if ($projects->isEmpty()) {
            return null;
        }

        return $projects->firstWhere('id', $request->integer('project'))
            ?? $projects->first();
    }

    /**
     * Return the next priority number for a project.
     */
    private function nextPriorityForProject(int $projectId): int
    {
        return ((int) Task::query()
            ->where('project_id', $projectId)
            ->max('priority')) + 1;
    }

    /**
     * Compact task priorities so they remain 1, 2, 3... within a project.
     */
    private function normalizePriorities(int $projectId): void
    {
        Task::query()
            ->where('project_id', $projectId)
            ->orderBy('priority')
            ->orderBy('created_at')
            ->get()
            ->each(function (Task $task, int $index): void {
                $task->update([
                    'priority' => $index + 1,
                ]);
            });
    }
}
