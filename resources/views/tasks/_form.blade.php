@php
    $selectedProjectId = old(
        'project_id',
        $selectedProjectId ?? $task->project_id ?? null
    );
@endphp

<div class="mb-3">
    <label for="project_id" class="form-label">
        Project
    </label>

    <select
        id="project_id"
        name="project_id"
        class="form-select @error('project_id') is-invalid @enderror"
        required
    >
        @foreach ($projects as $project)
            <option
                value="{{ $project->id }}"
                @selected((int) $selectedProjectId === $project->id)
            >
                {{ $project->name }}
            </option>
        @endforeach
    </select>

    @error('project_id')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="mb-3">
    <label for="name" class="form-label">
        Task name
    </label>

    <input
        type="text"
        id="name"
        name="name"
        value="{{ old('name', $task->name ?? '') }}"
        class="form-control @error('name') is-invalid @enderror"
        maxlength="255"
        required
        autofocus
    >

    @error('name')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

@isset($task)
    <div class="mb-3">
        <label class="form-label">
            Priority
        </label>

        <input
            type="text"
            value="{{ $task->priority }}"
            class="form-control"
            readonly
        >
    </div>
@endisset

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        {{ $submitLabel }}
    </button>

    <a
        href="{{ route('tasks.index', $selectedProjectId ? ['project' => $selectedProjectId] : []) }}"
        class="btn btn-outline-secondary"
    >
        Cancel
    </a>
</div>
