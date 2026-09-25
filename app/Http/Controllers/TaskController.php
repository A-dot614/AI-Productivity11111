<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\BulkActionRequest;
use App\Http\Requests\Task\RescheduleTaskRequest;
use App\Http\Requests\Task\StoreSubtaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\ToggleStatusRequest;
use App\Http\Requests\Task\ToggleSubtaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Subtask;
use App\Models\Task;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\TagRepositoryInterface;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\Task\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct(
        protected readonly TaskRepositoryInterface $tasks,
        protected readonly CategoryRepositoryInterface $categories,
        protected readonly TagRepositoryInterface $tags,
        protected readonly TaskService $service,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Task::class);

        $query = $this->tasks->queryForUser(auth()->id());

        $filters = $request->validate([
            'status' => ['nullable', 'string', 'in:pending,in_progress,completed,archived'],
            'priority' => ['nullable', 'string', 'in:low,medium,high,urgent'],
            'category' => ['nullable', 'uuid'],
            'tag' => ['nullable', 'uuid'],
            'due' => ['nullable', 'string', 'in:today,overdue,upcoming'],
            'q' => ['nullable', 'string', 'max:120'],
            'sort' => ['nullable', 'string', 'in:due_date,priority,title,created_at'],
        ]);

        $status = $filters['status'] ?? 'active';

        if ($status === 'active') {
            $query->active();
        } else {
            $query->where('status', $status);
        }

        if (filled($filters['priority'] ?? null)) {
            $query->withPriority($filters['priority']);
        }

        if (filled($filters['category'] ?? null)) {
            $query->inCategory($filters['category']);
        }

        if (filled($filters['tag'] ?? null)) {
            $query->tagged($filters['tag']);
        }

        match ($filters['due'] ?? null) {
            'today' => $query->dueToday(),
            'overdue' => $query->overdue(),
            'upcoming' => $query->upcoming(),
            default => null,
        };

        if (filled($filters['q'] ?? null)) {
            $query->search($filters['q']);
        }

        $query->orderBy($filters['sort'] ?? 'due_date');

        $tasks = $this->tasks->paginate($query, 12);

        return view('tasks.index', [
            'tasks' => $tasks,
            'categories' => $this->categories->queryForUser(auth()->id())->get(),
            'tags' => $this->tags->queryForUser(auth()->id())->get(),
            'filters' => $filters,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Task::class);

        return view('tasks.create', [
            'categories' => $this->categories->queryForUser(auth()->id())->get(),
            'tags' => $this->tags->queryForUser(auth()->id())->get(),
            'task' => new Task,
        ]);
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $task = $this->service->createForUser(
            user: $request->user(),
            data: $request->only(['title', 'description', 'priority', 'status', 'category_id', 'due_date', 'scheduled_at', 'estimated_minutes', 'recurrence']),
            tagIds: $request->input('tags', []),
            subtaskTitles: $request->input('subtasks', []),
        );

        return redirect()
            ->route('tasks.show', $task)
            ->with('toast', ['type' => 'success', 'title' => 'Task created', 'message' => '"'.$task->title.'" has been added.']);
    }

    public function show(Task $task): View
    {
        $this->authorize('view', $task);

        $task->load(['category', 'tags', 'subtasks', 'notes', 'parent', 'children']);

        return view('tasks.show', [
            'task' => $task,
            'categories' => $this->categories->queryForUser(auth()->id())->get(),
            'tags' => $this->tags->queryForUser(auth()->id())->get(),
        ]);
    }

    public function edit(Task $task): View
    {
        $this->authorize('update', $task);

        return view('tasks.edit', [
            'task' => $task,
            'categories' => $this->categories->queryForUser(auth()->id())->get(),
            'tags' => $this->tags->queryForUser(auth()->id())->get(),
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $this->service->updateTask(
            task: $task,
            data: $request->only(['title', 'description', 'priority', 'status', 'category_id', 'due_date', 'scheduled_at', 'estimated_minutes', 'actual_minutes', 'recurrence']),
            tagIds: $request->input('tags', []),
        );

        return redirect()
            ->route('tasks.show', $task)
            ->with('toast', ['type' => 'success', 'title' => 'Task updated', 'message' => 'Changes have been saved.']);
    }

    public function updateStatus(ToggleStatusRequest $request, Task $task): RedirectResponse
    {
        $task = match ($request->validated('status')) {
            Task::STATUS_COMPLETED => $this->service->completeTask($task, $request->integer('actual_minutes')),
            Task::STATUS_IN_PROGRESS => $this->service->toggleInProgress($task),
            Task::STATUS_ARCHIVED => $this->service->archiveTask($task),
            default => $task->status === Task::STATUS_ARCHIVED
                ? $this->service->restoreTask($task)
                : $this->service->reopenTask($task),
        };

        return back()->with('toast', ['type' => 'success', 'title' => 'Status updated', 'message' => 'Task is now "'.$task->status.'".']);
    }

    public function reschedule(RescheduleTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update(['due_date' => $request->validated('due_date')]);

        return back()->with('toast', ['type' => 'success', 'title' => 'Task rescheduled', 'message' => 'The due date has been updated.']);
    }

    public function restore(Task $task): RedirectResponse
    {
        $this->authorize('restore', $task);

        $this->service->restoreTask($task);

        return redirect()->route('tasks.index', ['status' => 'archived'])
            ->with('toast', ['type' => 'success', 'title' => 'Task restored', 'message' => 'The task is active again.']);
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);

        $this->service->deleteTask($task);

        return redirect()->route('tasks.index')
            ->with('toast', ['type' => 'success', 'title' => 'Task deleted', 'message' => 'The task has been moved to the trash.']);
    }

    public function bulk(BulkActionRequest $request): RedirectResponse
    {
        $ids = $request->validated('selected');
        $action = $request->validated('action');

        match ($action) {
            'complete' => $this->tasks->bulkComplete($ids, $request->user()->id),
            'archive' => $this->tasks->bulkArchive($ids, $request->user()->id),
            'delete' => Task::forUser($request->user()->id)->whereIn('id', $ids)->get()->each(fn (Task $t) => $t->delete()),
            'restore' => Task::forUser($request->user()->id)->onlyTrashed()->whereIn('id', $ids)->get()->each(fn (Task $t) => $t->restore()),
        };

        return back()->with('toast', ['type' => 'success', 'title' => 'Bulk action applied', 'message' => count($ids).' task(s) updated.']);
    }

    public function storeSubtask(StoreSubtaskRequest $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $task->subtasks()->create([
            'title' => $request->validated('title'),
            'sort_order' => $task->subtasks()->max('sort_order') + 1,
        ]);

        return back()->with('toast', ['type' => 'success', 'title' => 'Subtask added', 'message' => 'The subtask has been added.']);
    }

    public function toggleSubtask(ToggleSubtaskRequest $request, Task $task, Subtask $subtask): RedirectResponse
    {
        $this->authorize('update', $task);

        if ($subtask->task_id !== $task->id) {
            abort(404);
        }

        $subtask = $this->service->toggleSubtask($subtask);

        return back()->with('toast', ['type' => 'success', 'title' => 'Subtask updated', 'message' => $subtask->is_completed ? 'Subtask completed.' : 'Subtask reopened.']);
    }

    public function destroySubtask(Task $task, Subtask $subtask): RedirectResponse
    {
        $this->authorize('update', $task);

        if ($subtask->task_id !== $task->id) {
            abort(404);
        }

        $subtask->delete();

        return back()->with('toast', ['type' => 'success', 'title' => 'Subtask removed', 'message' => 'The subtask has been deleted.']);
    }
}
