<x-app-layout>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('tasks.index') }}" class="btn-ghost p-2">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $task->title }}</h1>
                <div class="mt-1 flex flex-wrap items-center gap-2">
                    <x-status-badge :status="$task->status" />
                    <x-priority-badge :priority="$task->priority" />
                    @if ($task->category)
                        <span class="badge" style="color: {{ $task->category->color }}; border-color: {{ $task->category->color }}33">{{ $task->category->name }}</span>
                    @endif
                    @foreach ($task->tags as $tag)
                        <span class="badge-slate">#{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="flex shrink-0 items-center gap-2">
            <form action="{{ route('tasks.status', $task) }}" method="POST">
                @csrf
                @method('PATCH')
                @if ($task->is_completed)
                    <input type="hidden" name="status" value="pending">
                    <button class="btn-secondary">Reopen</button>
                @else
                    <input type="hidden" name="status" value="completed">
                    <button class="btn-primary">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        Complete
                    </button>
                @endif
            </form>
            <a href="{{ route('tasks.edit', $task) }}" class="btn-secondary">Edit</a>
            <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete this task?')">
                @csrf
                @method('DELETE')
                <button class="btn-ghost p-2 text-rose-500">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            {{-- Description --}}
            <x-card title="Description">
                @if ($task->description)
                    <div class="markdown-body text-sm text-slate-700 dark:text-slate-300">{!! app(\App\Services\Markdown\MarkdownService::class)->render($task->description) !!}</div>
                @else
                    <p class="text-sm text-slate-400">No description provided.</p>
                @endif
            </x-card>

            {{-- Subtasks --}}
            <x-card title="Subtasks" subtitle="{{ $task->subtasks->count() ? $task->subtasks->where('is_completed', true)->count().' of '.$task->subtasks->count().' done' : null }}">
                @if ($task->subtasks->isNotEmpty())
                    <div x-data="{ collapsed: false }">
                        <ul class="space-y-1.5" x-show="!collapsed">
                            @foreach ($task->subtasks as $subtask)
                                <li class="flex items-center gap-3 rounded-lg px-2 py-1.5 transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                    <form action="{{ route('tasks.subtasks.toggle', [$task, $subtask]) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="flex h-5 w-5 items-center justify-center rounded-full border-2 transition {{ $subtask->is_completed ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-slate-300 hover:border-emerald-500 dark:border-slate-600' }}">
                                            @if ($subtask->is_completed)
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                            @endif
                                        </button>
                                    </form>
                                    <span class="flex-1 text-sm {{ $subtask->is_completed ? 'text-slate-400 line-through' : 'text-slate-700 dark:text-slate-200' }}">{{ $subtask->title }}</span>
                                    <form action="{{ route('tasks.subtasks.destroy', [$task, $subtask]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-slate-300 transition hover:text-rose-500 dark:text-slate-600">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                        <button x-show="collapsed" x-cloak @click="collapsed = false" class="mt-2 text-xs font-semibold text-brand-600 dark:text-brand-400">Show {{ $task->subtasks->count() }} subtasks</button>
                        <button x-show="!collapsed && {{ $task->subtasks->count() }} > 5" @click="collapsed = true" class="mt-2 text-xs font-semibold text-brand-600 dark:text-brand-400">Collapse</button>
                    </div>
                @endif

                <form action="{{ route('tasks.subtasks.store', $task) }}" method="POST" class="mt-3 flex gap-2" x-data>
                    @csrf
                    <input type="text" name="title" required placeholder="Add a subtask…" class="input flex-1">
                    <button type="submit" class="btn-secondary shrink-0">Add</button>
                </form>
            </x-card>
        </div>

        <div class="space-y-6">
            {{-- Schedule --}}
            <x-card title="Schedule">
                <dl class="space-y-3 text-sm">
                    @if ($task->due_date)
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-400">Due</dt>
                            <dd class="font-medium {{ $task->is_overdue ? 'text-rose-500' : 'text-slate-700 dark:text-slate-200' }}">{{ $task->due_date->format('M j, Y · g:i A') }}</dd>
                        </div>
                    @endif
                    @if ($task->scheduled_at)
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-400">Scheduled</dt>
                            <dd class="font-medium text-slate-700 dark:text-slate-200">{{ $task->scheduled_at->format('M j, Y · g:i A') }}</dd>
                        </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400">Estimated</dt>
                        <dd class="font-medium text-slate-700 dark:text-slate-200">{{ $task->estimated_minutes ? $task->estimated_minutes.' min' : '—' }}</dd>
                    </div>
                    @if ($task->actual_minutes !== null)
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-400">Actual</dt>
                            <dd class="font-medium text-slate-700 dark:text-slate-200">{{ $task->actual_minutes }} min</dd>
                        </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400">Repeats</dt>
                        <dd class="font-medium capitalize text-slate-700 dark:text-slate-200">{{ $task->recurrence }}</dd>
                    </div>
                    @if ($task->completed_at)
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-400">Completed</dt>
                            <dd class="font-medium text-slate-700 dark:text-slate-200">{{ $task->completed_at->format('M j, g:i A') }}</dd>
                        </div>
                    @endif
                </dl>
            </x-card>

            {{-- Related notes --}}
            @if ($task->notes->isNotEmpty())
                <x-card title="Linked notes">
                    <ul class="space-y-2">
                        @foreach ($task->notes as $note)
                            <li>
                                <a href="{{ route('notes.show', $note) }}" class="block rounded-lg border border-slate-100 p-3 text-sm transition hover:border-brand-200 hover:bg-brand-50/40 dark:border-slate-800 dark:hover:border-brand-500/30">
                                    <span class="font-medium text-slate-700 dark:text-slate-200">{{ $note->title }}</span>
                                    <span class="mt-0.5 block text-xs text-slate-400">{{ $note->updated_at->diffForHumans() }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-card>
            @endif

            {{-- Child tasks --}}
            @if ($task->children->isNotEmpty())
                <x-card title="Subtasks from AI breakdown">
                    <ul class="space-y-2">
                        @foreach ($task->children as $child)
                            <li class="flex items-center gap-2 text-sm">
                                <span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span>
                                <a href="{{ route('tasks.show', $child) }}" class="text-slate-600 hover:text-brand-600 dark:text-slate-300">{{ $child->title }}</a>
                                @if ($child->status === 'completed')
                                    <span class="badge-green ml-auto">Done</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>
