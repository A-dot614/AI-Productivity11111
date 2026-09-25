<?php

namespace App\Services\Task;

use App\Models\Category;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;

class TaskService
{
    public function __construct(
        protected readonly TaskRepositoryInterface $tasks,
    ) {}

    /**
     * Create a task with optional tags and subtasks.
     *
     * @param  array<string, mixed>  $data
     * @param  list<string>  $tagIds
     * @param  list<string>  $subtaskTitles
     */
    public function createForUser(User $user, array $data, array $tagIds = [], array $subtaskTitles = []): Task
    {
        $task = $this->tasks->create([
            ...$data,
            'user_id' => $user->id,
            'status' => $data['status'] ?? Task::STATUS_PENDING,
        ]);

        $this->attachTags($task, $tagIds);
        $this->syncSubtasks($task, $subtaskTitles);

        return $task->load(['category', 'tags', 'subtasks']);
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<string>  $tagIds
     */
    public function updateTask(Task $task, array $data, array $tagIds = []): Task
    {
        $task = $this->tasks->update($task, $data);

        $this->attachTags($task, $tagIds);

        return $task->load(['category', 'tags', 'subtasks']);
    }

    public function completeTask(Task $task, ?int $actualMinutes = null): Task
    {
        if ($actualMinutes !== null) {
            $task->update(['actual_minutes' => max(0, $actualMinutes)]);
        }

        if (! $task->isCompleted) {
            $task->markCompleted();
        }

        return $task->refresh();
    }

    public function reopenTask(Task $task): Task
    {
        if ($task->isCompleted) {
            $task->reopen();
        }

        return $task->refresh();
    }

    public function toggleInProgress(Task $task): Task
    {
        if ($task->status === Task::STATUS_IN_PROGRESS) {
            $task->update(['status' => Task::STATUS_PENDING, 'completed_at' => null]);
        } else {
            $task->markInProgress();
        }

        return $task->refresh();
    }

    public function archiveTask(Task $task): Task
    {
        $task->update(['status' => Task::STATUS_ARCHIVED]);

        return $task->refresh();
    }

    public function restoreTask(Task $task): Task
    {
        $task->update(['status' => Task::STATUS_PENDING, 'completed_at' => null]);

        return $task->refresh();
    }

    public function deleteTask(Task $task): void
    {
        $this->tasks->delete($task);
    }

    public function toggleSubtask(Subtask $subtask): Subtask
    {
        return $subtask->is_completed ? $subtask->reopen() : $subtask->markCompleted();
    }

    /**
     * Create subtasks from an array of titles.
     *
     * @param  list<string>  $titles
     * @return list<Subtask>
     */
    public function syncSubtasks(Task $task, array $titles): array
    {
        $created = [];
        $order = 0;

        foreach (array_values(array_filter(array_map('trim', $titles))) as $title) {
            $created[] = $task->subtasks()->create([
                'title' => $title,
                'is_completed' => false,
                'sort_order' => $order++,
            ]);
        }

        return $created;
    }

    /**
     * Resolve a category by name for a user, creating it when needed.
     */
    public function resolveCategory(User $user, string $name): Category
    {
        return Category::query()
            ->where('user_id', $user->id)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($name))])
            ->firstOrCreate(
                ['user_id' => $user->id, 'name' => trim($name)],
                ['color' => $this->randomColor()],
            );
    }

    protected function attachTags(Task $task, array $tagIds): void
    {
        $task->tags()->sync(array_values(array_filter($tagIds)));
    }

    protected function randomColor(): string
    {
        $palette = ['#6366f1', '#8b5cf6', '#ec4899', '#ef4444', '#f59e0b', '#10b981', '#06b6d4'];

        return $palette[array_rand($palette)];
    }
}
