@extends('layouts.app')

@section('title', 'Create Task')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4">Create task</h1>

                    <form action="{{ route('tasks.store') }}" method="POST">
                        @csrf

                        @include('tasks._form', [
                            'projects' => $projects,
                            'selectedProjectId' => $selectedProjectId,
                            'submitLabel' => 'Create task',
                        ])
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
