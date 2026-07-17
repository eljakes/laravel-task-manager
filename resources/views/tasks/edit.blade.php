@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4">Edit task</h1>

                    <form
                        action="{{ route('tasks.update', $task) }}"
                        method="POST"
                    >
                        @csrf
                        @method('PUT')

                        @include('tasks._form', [
                            'projects' => $projects,
                            'task' => $task,
                            'submitLabel' => 'Save changes',
                        ])
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
