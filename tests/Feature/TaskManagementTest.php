<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_tasks_are_filtered_by_selected_project(): void
    {
        $website = Project::create(['name' => 'Website']);
        $mobile = Project::create(['name' => 'Mobile App']);

        Task::create([
            'project_id' => $website->id,
            'name' => 'Build homepage',
            'priority' => 1,
        ]);

        Task::create([
            'project_id' => $mobile->id,
            'name' => 'Design login screen',
            'priority' => 1,
        ]);

        $response = $this->get(route('tasks.index', ['project' => $website->id]));

        $response
            ->assertOk()
            ->assertSee('name="project"', false)
            ->assertSee('Build homepage')
            ->assertDontSee('Design login screen');
    }

    public function test_task_list_renders_drag_and_drop_reorder_hooks(): void
    {
        $project = Project::create(['name' => 'Website']);

        Task::create([
            'project_id' => $project->id,
            'name' => 'First',
            'priority' => 1,
        ]);

        Task::create([
            'project_id' => $project->id,
            'name' => 'Second',
            'priority' => 2,
        ]);

        $response = $this->get(route('tasks.index', ['project' => $project->id]));

        $response
            ->assertOk()
            ->assertSee('id="task-list"', false)
            ->assertSee('data-reorder-url', false)
            ->assertSee('draggable="true"', false)
            ->assertSee('Drag to reorder');
    }

    public function test_creating_a_task_assigns_the_next_project_priority(): void
    {
        $project = Project::create(['name' => 'Website']);

        Task::create([
            'project_id' => $project->id,
            'name' => 'Set up hosting',
            'priority' => 1,
        ]);

        $response = $this->post(route('tasks.store'), [
            'project_id' => $project->id,
            'name' => 'Write launch checklist',
        ]);

        $response->assertRedirect(route('tasks.index', ['project' => $project->id]));

        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->id,
            'name' => 'Write launch checklist',
            'priority' => 2,
        ]);
    }

    public function test_reordering_tasks_updates_priorities_from_top_to_bottom(): void
    {
        $project = Project::create(['name' => 'Website']);

        $first = Task::create([
            'project_id' => $project->id,
            'name' => 'First',
            'priority' => 1,
        ]);

        $second = Task::create([
            'project_id' => $project->id,
            'name' => 'Second',
            'priority' => 2,
        ]);

        $third = Task::create([
            'project_id' => $project->id,
            'name' => 'Third',
            'priority' => 3,
        ]);

        $response = $this->postJson(route('tasks.reorder'), [
            'project_id' => $project->id,
            'tasks' => [
                $third->id,
                $first->id,
                $second->id,
            ],
        ]);

        $response->assertOk();

        $this->assertSame(1, $third->fresh()->priority);
        $this->assertSame(2, $first->fresh()->priority);
        $this->assertSame(3, $second->fresh()->priority);
    }

    public function test_moving_a_task_to_another_project_keeps_priorities_sequential(): void
    {
        $sourceProject = Project::create(['name' => 'Website']);
        $targetProject = Project::create(['name' => 'Mobile App']);

        $sourceTask = Task::create([
            'project_id' => $sourceProject->id,
            'name' => 'Keep me',
            'priority' => 1,
        ]);

        $movingTask = Task::create([
            'project_id' => $sourceProject->id,
            'name' => 'Move me',
            'priority' => 2,
        ]);

        $targetTask = Task::create([
            'project_id' => $targetProject->id,
            'name' => 'Already there',
            'priority' => 1,
        ]);

        $response = $this->put(route('tasks.update', $movingTask), [
            'project_id' => $targetProject->id,
            'name' => 'Move me',
        ]);

        $response->assertRedirect(route('tasks.index', ['project' => $targetProject->id]));

        $this->assertSame(1, $sourceTask->fresh()->priority);
        $this->assertSame(1, $targetTask->fresh()->priority);
        $this->assertSame(2, $movingTask->fresh()->priority);
        $this->assertSame($targetProject->id, $movingTask->fresh()->project_id);
    }

    public function test_deleting_a_task_compacts_remaining_priorities(): void
    {
        $project = Project::create(['name' => 'Website']);

        $first = Task::create([
            'project_id' => $project->id,
            'name' => 'First',
            'priority' => 1,
        ]);

        $second = Task::create([
            'project_id' => $project->id,
            'name' => 'Second',
            'priority' => 2,
        ]);

        $third = Task::create([
            'project_id' => $project->id,
            'name' => 'Third',
            'priority' => 3,
        ]);

        $response = $this->delete(route('tasks.destroy', $second));

        $response->assertRedirect(route('tasks.index', ['project' => $project->id]));

        $this->assertSame(1, $first->fresh()->priority);
        $this->assertSame(2, $third->fresh()->priority);
        $this->assertDatabaseMissing('tasks', [
            'id' => $second->id,
        ]);
    }
}
