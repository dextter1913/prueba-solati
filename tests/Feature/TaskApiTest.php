<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_their_tasks(): void
    {
        $user = User::factory()->create();
        $ownTasks = Task::factory()->count(3)->for($user)->create();
        $otherTask = Task::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/tasks');

        $response->assertOk()->assertJson(function (AssertableJson $json): void {
            $json->where('message', 'Listado de tareas.')
                ->has('data', 3)
                ->has('links')
                ->has('meta');
        });

        $ids = collect($response->json('data'))->pluck('id')->sort()->values();

        $this->assertEqualsCanonicalizing($ownTasks->pluck('id')->sort()->values()->all(), $ids->all());
        $this->assertNotContains($otherTask->id, $ids->all());
    }

    public function test_list_respects_per_page_cap_of_50(): void
    {
        $user = User::factory()->create();
        Task::factory()->count(55)->for($user)->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/tasks?per_page=100');

        $response->assertOk()
            ->assertJsonPath('meta.per_page', 50)
            ->assertJsonCount(50, 'data');
    }

    public function test_user_can_create_task_with_default_pending_status(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/tasks', [
            'title' => 'Redactar documentación',
        ]);

        $response->assertCreated()
            ->assertJsonPath('message', 'Tarea creada correctamente.')
            ->assertJsonPath('data.title', 'Redactar documentación')
            ->assertJsonPath('data.status', TaskStatus::Pending->value);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Redactar documentación',
            'status' => TaskStatus::Pending->value,
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_view_single_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/tasks/{$task->id}");

        $response->assertOk()
            ->assertJsonPath('message', 'Detalle de la tarea.')
            ->assertJsonPath('data.id', $task->id)
            ->assertJsonPath('data.title', $task->title);
    }

    public function test_user_cannot_view_task_from_another_user(): void
    {
        $user = User::factory()->create();
        $otherTask = Task::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/tasks/{$otherTask->id}");

        $response->assertNotFound()
            ->assertJsonPath('message', 'Tarea no encontrada.');
    }

    public function test_user_can_update_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->pending()->create();

        Sanctum::actingAs($user);

        $response = $this->patchJson("/api/v1/tasks/{$task->id}", [
            'title' => 'Título actualizado',
            'status' => TaskStatus::Completed->value,
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Tarea actualizada correctamente.')
            ->assertJsonPath('data.title', 'Título actualizado')
            ->assertJsonPath('data.status', TaskStatus::Completed->value);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Título actualizado',
            'status' => TaskStatus::Completed->value,
        ]);
    }

    public function test_update_requires_at_least_one_field(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        Sanctum::actingAs($user);

        $response = $this->patchJson("/api/v1/tasks/{$task->id}", []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['update']);
    }

    public function test_user_can_delete_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/v1/tasks/{$task->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }
}
