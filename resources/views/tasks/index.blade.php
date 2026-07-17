@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1">Tasks</h1>
            <p class="text-muted mb-0">
                {{ $selectedProject ? $selectedProject->name : 'No project selected' }}
            </p>
        </div>

        <div class="d-flex flex-column flex-sm-row gap-2">
            @if ($selectedProject)
                <a
                    href="{{ route('tasks.create', ['project' => $selectedProject->id]) }}"
                    class="btn btn-primary"
                >
                    Add task
                </a>
            @endif

            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                Manage projects
            </a>
        </div>
    </div>

    @if ($projects->isEmpty())
        <div class="alert alert-info">
            No projects have been created yet.

            <a href="{{ route('projects.create') }}" class="alert-link">
                Create a project
            </a>
            to start adding tasks.
        </div>
    @else
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('tasks.index') }}" method="GET">
                    <label for="project" class="form-label">
                        Project
                    </label>

                    <div class="row g-2">
                        <div class="col-md-8 col-lg-6">
                            <select
                                id="project"
                                name="project"
                                class="form-select"
                                onchange="this.form.submit()"
                            >
                                @foreach ($projects as $project)
                                    <option
                                        value="{{ $project->id }}"
                                        @selected($selectedProject?->is($project))
                                    >
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-outline-primary">
                                View tasks
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($tasks->isEmpty())
            <div class="alert alert-info">
                No tasks have been created for this project yet.
            </div>
        @else
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="drag-handle"></th>
                                <th>Priority</th>
                                <th>Task</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>

                        <tbody
                            id="task-list"
                            data-project-id="{{ $selectedProject->id }}"
                            data-reorder-url="{{ route('tasks.reorder') }}"
                        >
                            @foreach ($tasks as $task)
                                <tr
                                    class="task-row"
                                    draggable="true"
                                    data-task-id="{{ $task->id }}"
                                >
                                    <td class="drag-handle text-muted" title="Drag to reorder">
                                        <span aria-hidden="true">&#8597;</span>
                                        <span class="visually-hidden">Drag to reorder</span>
                                    </td>

                                    <td class="fw-semibold" data-priority>
                                        {{ $task->priority }}
                                    </td>

                                    <td>
                                        {{ $task->name }}
                                    </td>

                                    <td>
                                        {{ $task->created_at->format('M j, Y g:i A') }}
                                    </td>

                                    <td>
                                        {{ $task->updated_at->format('M j, Y g:i A') }}
                                    </td>

                                    <td class="text-end table-actions">
                                        <a
                                            href="{{ route('tasks.edit', $task) }}"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            action="{{ route('tasks.destroy', $task) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Delete this task?');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                            >
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer text-muted small" id="reorder-status">
                    Drag rows to change priority.
                </div>
            </div>
        @endif
    @endif
@endsection

@push('scripts')
    @if ($selectedProject && $tasks->isNotEmpty())
        <script>
            (() => {
                const list = document.getElementById('task-list');
                const status = document.getElementById('reorder-status');
                const projectId = Number(list.dataset.projectId);
                const reorderUrl = list.dataset.reorderUrl;
                const token = document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content');
                let draggedRow = null;

                const rows = () => Array.from(list.querySelectorAll('.task-row'));

                const refreshPriorities = () => {
                    rows().forEach((row, index) => {
                        row.querySelector('[data-priority]').textContent = index + 1;
                    });
                };

                const saveOrder = async () => {
                    const taskIds = rows().map((row) => row.dataset.taskId);

                    status.textContent = 'Saving order...';

                    try {
                        const response = await fetch(reorderUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                            },
                            body: JSON.stringify({
                                project_id: projectId,
                                tasks: taskIds,
                            }),
                        });

                        if (!response.ok) {
                            throw new Error('Task order was not saved.');
                        }

                        status.textContent = 'Order saved.';
                    } catch (error) {
                        status.textContent = 'Order could not be saved. Refresh and try again.';
                    }
                };

                rows().forEach((row) => {
                    row.addEventListener('dragstart', (event) => {
                        draggedRow = row;
                        row.classList.add('dragging');
                        event.dataTransfer.effectAllowed = 'move';
                        event.dataTransfer.setData('text/plain', row.dataset.taskId);
                    });

                    row.addEventListener('dragend', () => {
                        row.classList.remove('dragging');
                        rows().forEach((taskRow) => taskRow.classList.remove('drag-over'));
                        draggedRow = null;
                        refreshPriorities();
                        saveOrder();
                    });

                    row.addEventListener('dragover', (event) => {
                        event.preventDefault();

                        const targetRow = event.target.closest('.task-row');

                        if (!draggedRow || !targetRow || draggedRow === targetRow) {
                            return;
                        }

                        const box = targetRow.getBoundingClientRect();
                        const shouldInsertAfter = event.clientY > box.top + box.height / 2;

                        targetRow.classList.add('drag-over');
                        list.insertBefore(
                            draggedRow,
                            shouldInsertAfter ? targetRow.nextSibling : targetRow
                        );
                    });

                    row.addEventListener('dragleave', () => {
                        row.classList.remove('drag-over');
                    });
                });
            })();
        </script>
    @endif
@endpush
