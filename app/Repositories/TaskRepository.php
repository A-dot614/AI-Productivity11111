<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskRepository implements TaskRepositoryInterface
{
    public function findForUser(int $userId, string $id): ?Task
    {
        return Task::query()
            ->with(['category', 'tags', 'subtasks', 'parent'])
            ->forUser($userId)
            ->find($id);
    }

    public function queryForUser(int $userId): Builder
    {
        return Task::query()
            ->with(['category', 'tags', 'subtasks'])
            ->withCount(['subtasks', 'subtasks as subtasks_completed_count' => fn (Builder $q) => $q->where('is_completed', true)])
            ->forUser($userId);
    }

    public function paginate(Builder $query, int $perPage = 15): LengthAwarePaginator
    {
        return $query->paginate($perPage)->withQueryString();
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task->refresh();
    }

    public function delete(Task $task): bool
    {
        return (bool) $task->delete();
    }

    public function restore(Task $task): bool
    {
        return $task->restore();
    }

    public function bulkComplete(array $ids, int $userId): int
    {
        $count = 0;

        Task::forUser($userId)->whereIn('id', $ids)->get()->each(function (Task $task) use (&$count) {
            if (! $task->isCompleted) {
                $task->markCompleted();
                $count++;
            }
        });

        return $count;
    }

    public function bulkArchive(array $ids, int $userId): int
    {
        return Task::forUser($userId)
            ->whereIn('id', $ids)
            ->where('status', '!=', Task::STATUS_ARCHIVED)
            ->update(['status' => Task::STATUS_ARCHIVED]);
    }
}
