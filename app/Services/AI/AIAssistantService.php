<?php

namespace App\Services\AI;

use App\Models\Note;
use App\Models\Task;
use App\Models\User;
use App\Services\AI\Exceptions\AIException;
use App\Services\Note\NoteService;
use App\Services\Productivity\ProductivityService;
use App\Services\Task\TaskService;
use Carbon\CarbonImmutable;

class AIAssistantService
{
    public function __construct(
        protected readonly AIService $ai,
        protected readonly TaskService $tasks,
        protected readonly NoteService $notes,
        protected readonly ProductivityService $productivity,
    ) {}

    /**
     * Create a task from natural language, falling back to a local parser
     * when the AI provider is unavailable.
     */
    public function createTaskFromRequest(User $user, string $request): Task
    {
        try {
            $response = $this->ai->generate(
                feature: 'natural_language_task',
                template: 'natural_language_task',
                params: ['request' => $request],
            );

            $data = $this->jsonFromResponse($response) ?? [];

            $title = trim((string) ($data['title'] ?? ''));
            $dueDate = $this->parseDate($data['due_date'] ?? null);
            $categoryName = trim((string) ($data['category'] ?? ''));
            $category = filled($categoryName) ? $this->tasks->resolveCategory($user, $categoryName) : null;

            return $this->tasks->createForUser($user, [
                'title' => filled($title) ? $title : $request,
                'description' => trim((string) ($data['description'] ?? '')) ?: null,
                'priority' => $this->normalizePriority($data['priority'] ?? 'medium'),
                'due_date' => $dueDate,
                'estimated_minutes' => is_numeric($data['estimated_minutes'] ?? null) ? (int) $data['estimated_minutes'] : null,
                'category_id' => $category?->id,
            ]);
        } catch (AIException) {
            $parsed = $this->parseRequestLocally($request);

            return $this->tasks->createForUser($user, $parsed);
        }
    }

    /**
     * Suggest a task (title + subtasks) from a natural language request
     * without persisting anything.
     *
     * @return array{title: string, subtasks: list<string>}
     */
    public function suggestTask(User $user, string $request): array
    {
        try {
            $response = $this->ai->generate(
                feature: 'natural_language_task',
                template: 'natural_language_task',
                params: ['request' => $request],
            );

            $data = $this->jsonFromResponse($response) ?? [];

            $title = trim((string) ($data['title'] ?? ''));

            return [
                'title' => filled($title) ? $title : $request,
                'subtasks' => $this->suggestSubtasks(filled($title) ? $title : $request),
            ];
        } catch (AIException) {
            return [
                'title' => $this->parseRequestLocally($request)['title'],
                'subtasks' => [],
            ];
        }
    }

    /**
     * @return list<string>
     */
    protected function suggestSubtasks(string $title): array
    {
        try {
            $response = $this->ai->generate(
                feature: 'task_breakdown',
                template: 'task_breakdown',
                params: [
                    'title' => $title,
                    'description' => 'No description provided.',
                    'estimated_minutes' => 60,
                ],
            );

            $steps = $this->jsonFromResponse($response);

            if (! is_array($steps)) {
                return [];
            }

            $steps = array_values(array_filter($steps, fn ($step) => is_string($step) && trim($step) !== ''));

            return array_slice($steps, 0, 8);
        } catch (AIException) {
            return [];
        }
    }

    /**
     * @return list<array{title: string}>
     */
    public function breakdownTask(Task $task): array
    {
        $response = $this->ai->generate(
            feature: 'task_breakdown',
            template: 'task_breakdown',
            params: [
                'title' => $task->title,
                'description' => $task->description ?? 'No description provided.',
                'estimated_minutes' => $task->estimated_minutes ?? 60,
            ],
        );

        $steps = $this->jsonFromResponse($response) ?? [];

        if (! is_array($steps) || empty($steps)) {
            throw AIException::emptyResponse('ai');
        }

        $titles = collect($steps)->map(fn ($step) => is_string($step) ? $step : (string) ($step['title'] ?? $step['name'] ?? ''))->filter(fn ($title) => filled($title))->values()->all();

        return array_map(fn (string $title) => ['title' => $title], $titles);
    }

    /**
     * Prioritize the user's active tasks and apply the suggested priorities.
     *
     * @return array<int, array<string, mixed>>
     */
    public function prioritizeTasks(User $user): array
    {
        $tasks = $user->tasks()
            ->active()
            ->whereNull('deleted_at')
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->limit(15)
            ->get(['id', 'title', 'priority', 'due_date', 'estimated_minutes']);

        if ($tasks->isEmpty()) {
            return [];
        }

        $response = $this->ai->generate(
            feature: 'task_prioritization',
            template: 'task_prioritization',
            params: ['tasks' => $tasks->map(fn (Task $task) => [
                'id' => $task->id,
                'title' => $task->title,
                'priority' => $task->priority,
                'due_date' => $task->due_date?->toDateString(),
                'estimated_minutes' => $task->estimated_minutes,
            ])->toJson()],
        );

        $suggestions = $this->jsonFromResponse($response) ?? [];

        if (! is_array($suggestions) || empty($suggestions)) {
            throw AIException::emptyResponse('ai');
        }

        $tasksById = $tasks->keyBy('id');

        return collect($suggestions)
            ->filter(fn ($item) => isset($tasksById[$item['id'] ?? null]))
            ->map(function (array $item) use ($tasksById) {
                $task = $tasksById[$item['id']];
                $priority = $this->normalizePriority($item['priority'] ?? $task->priority);

                $task->update(['priority' => $priority]);

                return [
                    'task' => $task,
                    'priority' => $priority,
                    'suggested_order' => (int) ($item['suggested_order'] ?? 999),
                    'reason' => (string) ($item['reason'] ?? ''),
                ];
            })
            ->sortBy('suggested_order')
            ->values()
            ->all();
    }

    /**
     * @return array{title: string, description: ?string}
     */
    public function rewriteTask(Task $task): array
    {
        $response = $this->ai->generate(
            feature: 'task_rewrite',
            template: 'task_rewrite',
            params: [
                'title' => $task->title,
                'description' => $task->description ?? '',
            ],
        );

        $data = $this->jsonFromResponse($response) ?? [];

        if (! isset($data['title'])) {
            throw AIException::emptyResponse('ai');
        }

        return [
            'title' => trim((string) $data['title']),
            'description' => filled(trim((string) ($data['description'] ?? ''))) ? trim((string) $data['description']) : null,
        ];
    }

    /**
     * @return array{estimated_minutes: int, confidence: string, reason: ?string}
     */
    public function estimateDuration(Task $task): array
    {
        $response = $this->ai->generate(
            feature: 'estimate_duration',
            template: 'estimate_duration',
            params: [
                'title' => $task->title,
                'description' => $task->description ?? 'No description provided.',
            ],
        );

        $data = $this->jsonFromResponse($response) ?? [];

        if (! is_numeric($data['estimated_minutes'] ?? null)) {
            throw AIException::emptyResponse('ai');
        }

        return [
            'estimated_minutes' => max(5, (int) $data['estimated_minutes']),
            'confidence' => (string) ($data['confidence'] ?? 'low'),
            'reason' => isset($data['reason']) ? (string) $data['reason'] : null,
        ];
    }

    /**
     * Plan today's schedule for the user and apply the scheduled times.
     *
     * @return array{plan: array<int, array<string, mixed>>, note: ?string}
     */
    public function dailyPlan(User $user): array
    {
        $today = CarbonImmutable::today();

        $tasks = $user->tasks()
            ->active()
            ->whereNull('deleted_at')
            ->orderBy('due_date')
            ->limit(15)
            ->get(['id', 'title', 'priority', 'due_date', 'estimated_minutes']);

        $response = $this->ai->generate(
            feature: 'daily_planning',
            template: 'daily_planning',
            params: [
                'available_minutes' => max(30, $user->productivity_goal_minutes),
                'tasks' => $tasks->map(fn (Task $task) => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'priority' => $task->priority,
                    'due_date' => $task->due_date?->format('Y-m-d H:i'),
                    'estimated_minutes' => $task->estimated_minutes,
                ])->toJson(),
            ],
        );

        $data = $this->jsonFromResponse($response) ?? [];

        if (! is_array($data['plan'] ?? null)) {
            throw AIException::emptyResponse('ai');
        }

        $tasksById = $tasks->keyBy('id');

        $plan = collect($data['plan'])
            ->filter(fn ($item) => isset($tasksById[$item['id'] ?? null]))
            ->map(function (array $item) use ($today, $tasksById) {
                $task = $tasksById[$item['id']];

                if (preg_match('/^([01]?\d|2[0-3]):([0-5]\d)$/', (string) ($item['start_time'] ?? ''), $m)) {
                    $task->update([
                        'scheduled_at' => $today->setTime((int) $m[1], (int) $m[2]),
                    ]);
                }

                return [
                    'task' => $task,
                    'start_time' => (string) ($item['start_time'] ?? ''),
                    'title' => (string) ($item['title'] ?? $task->title),
                ];
            })
            ->sortBy('start_time')
            ->values()
            ->all();

        return [
            'plan' => $plan,
            'note' => filled((string) ($data['note'] ?? '')) ? (string) $data['note'] : null,
        ];
    }

    /**
     * Distribute tasks across the next 7 days.
     *
     * @return array<int, array{date: string, tasks: array<int, array<string, mixed>>}>
     */
    public function weeklyPlan(User $user): array
    {
        $tasks = $user->tasks()
            ->active()
            ->whereNull('deleted_at')
            ->whereDate('due_date', '<=', CarbonImmutable::today()->addWeek())
            ->orderBy('due_date')
            ->limit(30)
            ->get(['id', 'title', 'priority', 'due_date', 'estimated_minutes']);

        $response = $this->ai->generate(
            feature: 'weekly_planning',
            template: 'weekly_planning',
            params: [
                'daily_capacity' => max(60, (int) ceil($user->productivity_goal_minutes / 2)),
                'tasks' => $tasks->map(fn (Task $task) => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'priority' => $task->priority,
                    'due_date' => $task->due_date?->toDateString(),
                    'estimated_minutes' => $task->estimated_minutes,
                ])->toJson(),
            ],
        );

        $data = $this->jsonFromResponse($response) ?? [];

        if (! is_array($data['days'] ?? null)) {
            throw AIException::emptyResponse('ai');
        }

        $tasksById = $tasks->keyBy('id');
        $days = collect($data['days'])->filter(fn ($day) => filled((string) ($day['date'] ?? '')))->take(7);

        $result = [];

        foreach ($days as $day) {
            $date = $this->parseDate($day['date'] ?? null);

            if ($date === null) {
                continue;
            }

            $assigned = collect($day['task_ids'] ?? [])
                ->filter(fn ($id) => isset($tasksById[$id]))
                ->map(function ($id) use ($date, $tasksById) {
                    $task = $tasksById[$id];
                    $task->update([
                        'scheduled_at' => $date->setTime(9, 0),
                        'due_date' => $task->due_date ?? $date->setTime(17, 0),
                    ]);

                    return $task;
                })
                ->values()
                ->all();

            $result[] = [
                'date' => $date->format('Y-m-d'),
                'tasks' => $assigned,
            ];
        }

        return $result;
    }

    /**
     * @return list<string>
     */
    public function productivitySuggestions(User $user): array
    {
        $from = CarbonImmutable::today()->subDays(6);
        $metrics = $this->productivity->metrics($user, $from, CarbonImmutable::today());

        $response = $this->ai->generate(
            feature: 'productivity_suggestions',
            template: 'productivity_suggestions',
            params: ['metrics' => json_encode([
                'completion_rate' => $metrics['completion_rate'].'%',
                'on_time_rate' => $metrics['on_time_rate'].'%',
                'focus_hours' => $metrics['focus_hours'],
                'average_task_time_minutes' => $metrics['average_completion_time'],
                'planned' => $metrics['planned'],
                'completed' => $metrics['completed'],
            ])],
        );

        $data = $this->jsonFromResponse($response) ?? [];

        if (! is_array($data['suggestions'] ?? null)) {
            throw AIException::emptyResponse('ai');
        }

        return collect($data['suggestions'])->map(fn ($s) => (string) $s)->filter()->values()->all();
    }

    public function summarizeNote(Note $note): string
    {
        $response = $this->ai->generate(
            feature: 'note_summarization',
            template: 'note_summarization',
            params: [
                'title' => $note->title,
                'content' => str($note->content)->limit(4000),
            ],
        );

        return trim($response->content);
    }

    /**
     * Convert a note into tasks.
     *
     * @return array<int, Task>
     */
    public function notesToTasks(User $user, Note $note): array
    {
        $response = $this->ai->generate(
            feature: 'note_to_tasks',
            template: 'note_to_tasks',
            params: ['content' => str($note->content)->limit(4000)],
        );

        $items = $this->jsonFromResponse($response) ?? [];

        if (! is_array($items) || empty($items)) {
            throw AIException::emptyResponse('ai');
        }

        $created = [];

        foreach ($items as $item) {
            $title = trim((string) ($item['title'] ?? ''));

            if (blank($title)) {
                continue;
            }

            $created[] = $this->tasks->createForUser($user, [
                'title' => $title,
                'description' => filled(trim((string) ($item['description'] ?? ''))) ? trim((string) $item['description']) : null,
                'priority' => $this->normalizePriority($item['priority'] ?? 'medium'),
                'estimated_minutes' => is_numeric($item['estimated_minutes'] ?? null) ? (int) $item['estimated_minutes'] : null,
            ]);
        }

        return $created;
    }

    public function dailyDigest(User $user): string
    {
        $tasks = $user->tasks()
            ->active()
            ->whereDate('due_date', CarbonImmutable::today())
            ->orderBy('due_date')
            ->get(['title', 'priority', 'due_date']);

        if ($tasks->isEmpty()) {
            return 'You have no tasks scheduled for today. A perfect day to plan ahead!';
        }

        $response = $this->ai->generate(
            feature: 'daily_digest',
            template: 'daily_digest',
            params: ['tasks' => $tasks->map(fn (Task $task) => $task->title.' ('.$task->priority.($task->due_date ? ', due '.$task->due_date->format('H:i') : '').')')->implode(PHP_EOL)],
            userId: $user->id,
        );

        return trim($response->content);
    }

    /**
     * Decode JSON from a provider response, tolerating code fences.
     */
    protected function jsonFromResponse($response): ?array
    {
        $decoded = json_decode($response->cleanedContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $decoded;
    }

    protected function normalizePriority(mixed $value): string
    {
        $value = strtolower((string) $value);

        return in_array($value, Task::PRIORITIES, true) ? $value : Task::PRIORITY_MEDIUM;
    }

    protected function parseDate(mixed $value): ?CarbonImmutable
    {
        if (blank($value)) {
            return null;
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Local heuristic parser used when the AI provider is unavailable.
     *
     * @return array<string, mixed>
     */
    protected function parseRequestLocally(string $request): array
    {
        $lower = mb_strtolower($request);
        $priority = Task::PRIORITY_MEDIUM;
        $dueDate = null;
        $estimatedMinutes = null;

        if (str_contains($lower, 'tomorrow')) {
            $dueDate = CarbonImmutable::tomorrow()->setTime(17, 0);
        } elseif (str_contains($lower, 'today')) {
            $dueDate = CarbonImmutable::today()->setTime(17, 0);
        } elseif (preg_match('~(?:by|on)\s+(\d{1,2})[-/.](\d{1,2})(?:[-/.](\d{2,4}))?~', $request, $m)) {
            $year = isset($m[3]) && strlen($m[3]) === 2 ? '20'.$m[3] : ($m[3] ?? now()->year);
            $dueDate = CarbonImmutable::createFromFormat('Y-m-d', sprintf('%04d-%02d-%02d', $year, $m[1], $m[2]));
        }

        if (preg_match('/\b(urgent|asap|immediately)\b/i', $request)) {
            $priority = Task::PRIORITY_URGENT;
        } elseif (preg_match('/\b(high|important)\b/i', $request)) {
            $priority = Task::PRIORITY_HIGH;
        } elseif (preg_match('/\b(low|whenever|someday)\b/i', $request)) {
            $priority = Task::PRIORITY_LOW;
        }

        if (preg_match('/(\d+)\s*(?:min|minute)/i', $request, $m)) {
            $estimatedMinutes = (int) $m[1];
        } elseif (preg_match('/(\d+(?:\.\d+)?)\s*hours?/i', $request, $m)) {
            $estimatedMinutes = (int) round((float) $m[1] * 60);
        }

        $title = trim(preg_replace(
            '~(\b(today|tomorrow|urgent|asap|immediately|high|important|low|whenever|someday)\b|by\s+\d{1,2}[-/.]\d{1,2}(?:[-/.]\d{2,4})?|on\s+\d{1,2}[-/.]\d{1,2}(?:[-/.]\d{2,4})?|\d+\s*(?:min|minute|hours?))~i',
            '',
            $request
        ));

        $title = trim($title, ' .,-:;');

        return [
            'title' => filled($title) ? $title : $request,
            'description' => null,
            'priority' => $priority,
            'due_date' => $dueDate,
            'estimated_minutes' => $estimatedMinutes,
        ];
    }
}
