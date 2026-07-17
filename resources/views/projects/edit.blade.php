@extends('layouts.app')

@section('title', 'Edit Project')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4">Edit project</h1>

                    <form
                        action="{{ route('projects.update', $project) }}"
                        method="POST"
                    >
                        @csrf
                        @method('PUT')

                        @include('projects._form', [
                            'project' => $project,
                            'submitLabel' => 'Save changes',
                        ])
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection