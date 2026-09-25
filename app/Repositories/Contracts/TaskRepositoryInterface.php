<?php

namespace App\Repositories\Contracts;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
{
    public function findForUser(int $userId, string $id): ?Task;

    public function queryForUser(int $userId): Builder;

    public function paginate(Builder $query, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): Task;

    public function update(Task $task, array $data): Task;

    public function delete(Task $task): bool;

    public function restore(Task $task): bool;

    /**
     * @param  list<string>  $ids
     */
    public function bulkComplete(array $ids, int $userId): int;

    /**
     * @param  list<string>  $ids
     */
    public function bulkArchive(array $ids, int $userId): int;
}
