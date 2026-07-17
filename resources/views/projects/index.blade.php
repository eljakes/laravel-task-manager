@extends('layouts.app')

@section('title', 'Projects')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">Projects</h1>
            <p class="text-muted mb-0">
                Create projects and organise their tasks.
            </p>
        </div>

        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            Add project
        </a>
    </div>

    @if ($projects->isEmpty())
        <div class="alert alert-info">
            No projects have been created yet.
        </div>
    @else
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Project</th>
                            <th>Tasks</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td class="fw-semibold">
                                    {{ $project->name }}
                                </td>

                                <td>
                                    {{ $project->tasks_count }}
                                </td>

                                <td>
                                    {{ $project->created_at->format('M j, Y') }}
                                </td>

                                <td class="text-end">
                                    <a
                                        href="{{ route('projects.edit', $project) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route('projects.destroy', $project) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Delete this project and all its tasks?');"
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
        </div>
    @endif
@endsection