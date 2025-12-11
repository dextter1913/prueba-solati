<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function __construct(private readonly Task $task)
    {
    }

    public function paginateForUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->task
            ->newQuery()
            ->where('user_id', $userId)
            ->latest('id')
            ->paginate($perPage);
    }

    public function findForUser(int $userId, int $taskId): ?Task
    {
        return $this->task
            ->newQuery()
            ->where('user_id', $userId)
            ->find($taskId);
    }

    public function createForUser(int $userId, array $data): Task
    {
        return DB::transaction(function () use ($userId, $data) {
            return $this->task->newQuery()->create([
                ...$data,
                'user_id' => $userId,
            ]);
        });
    }

    public function update(Task $task, array $data): Task
    {
        return DB::transaction(function () use ($task, $data) {
            $task->fill($data);
            $task->save();

            return $task;
        });
    }

    public function delete(Task $task): void
    {
        DB::transaction(static function () use ($task) {
            $task->delete();
        });
    }
}
