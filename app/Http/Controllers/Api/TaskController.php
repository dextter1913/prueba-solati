<?php

namespace App\Http\Controllers\Api;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\StoreTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Repositories\TaskRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(private readonly TaskRepositoryInterface $tasks)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 50) : 10;

        $tasks = $this->tasks->paginateForUser($request->user()->id, $perPage);

        return TaskResource::collection($tasks)
            ->additional(['message' => 'Listado de tareas.'])
            ->response();
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['status'] ??= TaskStatus::Pending->value;

        $task = $this->tasks->createForUser($request->user()->id, $data);

        return TaskResource::make($task)
            ->additional(['message' => 'Tarea creada correctamente.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, int $task): JsonResponse
    {
        $taskModel = $this->tasks->findForUser($request->user()->id, $task);

        if (! $taskModel) {
            return response()->json(['message' => 'Tarea no encontrada.'], 404);
        }

        return TaskResource::make($taskModel)
            ->additional(['message' => 'Detalle de la tarea.'])
            ->response();
    }

    public function update(UpdateTaskRequest $request, int $task): JsonResponse
    {
        $taskModel = $this->tasks->findForUser($request->user()->id, $task);

        if (! $taskModel) {
            return response()->json(['message' => 'Tarea no encontrada.'], 404);
        }

        $updated = $this->tasks->update($taskModel, $request->validated());

        return TaskResource::make($updated)
            ->additional(['message' => 'Tarea actualizada correctamente.'])
            ->response();
    }

    public function destroy(Request $request, int $task): JsonResponse
    {
        $taskModel = $this->tasks->findForUser($request->user()->id, $task);

        if (! $taskModel) {
            return response()->json(['message' => 'Tarea no encontrada.'], 404);
        }

        $this->tasks->delete($taskModel);

        return response()->json(['message' => 'Tarea eliminada correctamente.'], 204);
    }
}
