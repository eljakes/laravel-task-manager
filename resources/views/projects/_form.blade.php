<div class="mb-3">
    <label for="name" class="form-label">
        Project name
    </label>

    <input
        type="text"
        id="name"
        name="name"
        value="{{ old('name', $project->name ?? '') }}"
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

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        {{ $submitLabel }}
    </button>

    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
        Cancel
    </a>
</div>