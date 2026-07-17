<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Display all projects.
     */
    public function index(): View
    {
        // Include the task count so the view does not need extra queries.
        $projects = Project::query()
            ->withCount('tasks')
            ->orderBy('name')
            ->get();

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a project.
     */
    public function create(): View
    {
        return view('projects.create');
    }

    /**
     * Save a newly created project.
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        Project::create($request->validated());

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Show the form for editing a project.
     */
    public function edit(Project $project): View
    {
        return view('projects.edit', compact('project'));
    }

    /**
     * Save changes to an existing project.
     */
    public function update(
        UpdateProjectRequest $request,
        Project $project
    ): RedirectResponse {
        $project->update($request->validated());

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Delete an existing project.
     */
    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
