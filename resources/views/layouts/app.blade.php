<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', 'Task Manager')
    </title>

    {{-- Bootstrap keeps the interface presentable without unnecessary custom CSS. --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        .drag-handle {
            cursor: grab;
            width: 2rem;
        }

        .dragging {
            opacity: 0.55;
        }

        .task-row {
            transition: background-color 0.15s ease;
        }

        .task-row.drag-over {
            background-color: #eef5ff;
        }

        .table-actions {
            white-space: nowrap;
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand" href="{{ route('tasks.index') }}">
                Task Manager
            </a>

            <div class="navbar-nav flex-row gap-3">
                <a
                    class="nav-link text-white"
                    href="{{ route('tasks.index') }}"
                >
                    Tasks
                </a>

                <a
                    class="nav-link text-white"
                    href="{{ route('projects.index') }}"
                >
                    Projects
                </a>
            </div>
        </div>
    </nav>

    <main class="container pb-5">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    ></script>

    @stack('scripts')
</body>
</html>
