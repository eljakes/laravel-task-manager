@extends('layouts.app')

@section('title', 'Create Project')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4">Create project</h1>

                    <form action="{{ route('projects.store') }}" method="POST">
                        @csrf

                        @include('projects._form', [
                            'submitLabel' => 'Create project',
                        ])
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection