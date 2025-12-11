<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
{
    public function paginateForUser(int $userId, int $perPage = 10): LengthAwarePaginator;

    public function findForUser(int $userId, int $taskId): ?Task;

    public function createForUser(int $userId, array $data): Task;

    public function update(Task $task, array $data): Task;

    public function delete(Task $task): void;
}
