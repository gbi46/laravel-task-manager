<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_task(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'Plan sprint retro',
            'description' => 'Outline talking points',
            'priority' => Task::PRIORITY_HIGH,
            'status' => Task::STATUS_PENDING,
            'due_date' => now()->addDay()->toDateString(),
        ]);

        $response->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Plan sprint retro',
            'priority' => Task::PRIORITY_HIGH,
        ]);
    }

    public function test_user_can_update_a_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create([
            'title' => 'Initial title',
            'status' => Task::STATUS_PENDING,
        ]);

        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => 'Updated title',
            'description' => 'Updated body copy',
            'priority' => Task::PRIORITY_LOW,
            'status' => Task::STATUS_IN_PROGRESS,
            'due_date' => now()->addWeek()->toDateString(),
        ]);

        $response->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated title',
            'status' => Task::STATUS_IN_PROGRESS,
        ]);
    }

    public function test_user_can_only_see_their_tasks(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Task::factory()->for($user)->count(2)->create();
        Task::factory()->for($otherUser)->count(1)->create([
            'title' => 'Hidden task',
        ]);

        $response = $this->actingAs($user)->get(route('tasks.index'));

        $response->assertOk();
        $response->assertDontSee('Hidden task');
    }

    public function test_user_can_update_task_status_from_quick_action(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create([
            'status' => Task::STATUS_PENDING,
            'due_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->patch(route('tasks.status', $task), [
            'status' => Task::STATUS_DONE,
        ]);

        $response->assertRedirect();

        $this->assertNotNull($task->fresh()->completed_at);
        $this->assertEquals(Task::STATUS_DONE, $task->fresh()->status);
    }
}
