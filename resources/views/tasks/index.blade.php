<x-app-layout>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Tasks</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage, filter and track everything on your plate.</p>
        </div>
        <a href="{{ route('tasks.create') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            New task
        </a>
    </div>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('tasks.index') }}" class="mb-6 card p-4" x-data="{ open: false }">
        <div class="flex flex-wrap items-center gap-2">
            @foreach (['active' => 'Active', 'pending' => 'Pending', 'in_progress' => 'In progress', 'completed' => 'Completed', 'archived' => 'Archived'] as $value => $label)
                <a href="{{ route('tasks.index', array_merge(request()->except(['status']), ['status' => $value])) }}"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ $status === $value ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-100 text-slate-500 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    {{ $label }}
                </a>
            @endforeach

            <div class="ms-auto flex items-center gap-2">
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search..." class="input !w-44 !py-2 pl-9 text-xs">
                </div>
                <button type="button" @click="open = !open" class="btn-ghost p-2" title="Filters">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                </button>
            </div>
        </div>

        <div x-show="open" x-cloak x-transition class="mt-4 grid grid-cols-2 gap-4 border-t border-slate-100 pt-4 sm:grid-cols-4 dark:border-slate-800">
            <div>
                <label class="label">Priority</label>
                <select name="priority" class="input">
                    <option value="">All</option>
                    @foreach (['urgent' => 'Urgent', 'high' => 'High', 'medium' => 'Medium', 'low' => 'Low'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('priority') == $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Category</label>
                <select name="category" class="input">
                    <option value="">All</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Tag</label>
                <select name="tag" class="input">
                    <option value="">All</option>
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->id }}" @selected(request('tag') == $tag->id)>{{ $tag->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Due</label>
                <select name="due" class="input">
                    <option value="">Any</option>
                    <option value="overdue" @selected(request('due') == 'overdue')>Overdue</option>
                    <option value="today" @selected(request('due') == 'today')>Today</option>
                    <option value="upcoming" @selected(request('due') == 'upcoming')>Upcoming</option>
                </select>
            </div>
            <div class="col-span-2 flex items-end gap-2 sm:col-span-4">
                <button type="submit" class="btn-primary !py-2 text-xs">Apply filters</button>
                <a href="{{ route('tasks.index') }}" class="btn-ghost px-3 py-2 text-xs">Reset</a>
                @if ($status === 'archived')
                    <span class="text-xs text-slate-400">Tasks in trash are kept for 30 days before permanent deletion.</span>
                @endif
            </div>
        </div>
    </form>

    {{-- Bulk actions --}}
    @if ($status !== 'archived' && $tasks->isNotEmpty())
        <form method="POST" action="{{ route('tasks.bulk') }}" class="mb-4 flex flex-wrap items-center gap-2" id="bulk-form">
            @csrf
            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <input type="checkbox" x-data x-on:change="document.querySelectorAll('.task-check').forEach(c => c.checked = $el.checked)" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">
                Select all
            </div>
            <select name="action" class="input !w-auto !py-1.5 text-xs">
                <option value="complete">Mark completed</option>
                <option value="archive">Archive</option>
                <option value="delete">Delete</option>
            </select>
            <button type="submit" class="btn-secondary !py-1.5 text-xs">Apply to selected</button>
        </form>
    @endif

    @if ($tasks->isEmpty())
        @php $createTaskLink = '<a href="'.route('tasks.create').'" class="btn-primary !py-2 text-xs">Create a task</a>'; @endphp
        <x-empty-state icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
            title="No {{ $status === 'archived' ? 'archived' : '' }} tasks found"
            message="{{ $status === 'archived' ? 'Deleted tasks appear here.' : 'Try adjusting your filters, or create a new task.' }}"
            :action="$createTaskLink" />
    @else
        <div class="card overflow-hidden">
            <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach ($tasks as $task)
                    <li class="group flex items-center gap-3 px-4 py-3 transition hover:bg-slate-50 dark:hover:bg-slate-800/50 sm:px-5 {{ $task->is_completed ? 'opacity-60' : '' }}">
                        @if ($status !== 'archived')
                            <input type="checkbox" name="selected[]" value="{{ $task->id }}" form="bulk-form" class="task-check rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">
                        @endif

                        <form action="{{ route('tasks.status', $task) }}" method="POST" @submit="event.preventDefault(); if (! this.dataset.confirm || confirm('Reopen task?')) this.submit();" data-confirm="{{ $task->is_completed ? '1' : '' }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $task->is_completed ? 'pending' : 'completed' }}">
                            <button type="submit" aria-label="Toggle completion"
                                class="flex h-5 w-5 items-center justify-center rounded-full border-2 transition {{ $task->is_completed ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-slate-300 hover:border-emerald-500 hover:bg-emerald-50 dark:border-slate-600' }}">
                                @if ($task->is_completed)
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                @endif
                            </button>
                        </form>

                        <div class="min-w-0 flex-1">
                            <a href="{{ route('tasks.show', $task) }}" class="block truncate text-sm font-medium text-slate-800 hover:text-brand-600 dark:text-slate-100 dark:hover:text-brand-400 {{ $task->is_completed ? 'line-through' : '' }}">{{ $task->title }}</a>
                            <div class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-slate-400 dark:text-slate-500">
                                @if ($task->due_date)
                                    <span class="{{ $task->is_overdue ? 'font-semibold text-rose-500' : '' }}">{{ $task->is_overdue ? 'Overdue · ' : '' }}{{ $task->due_date->format('M j, g:i A') }}</span>
                                @endif
                                @if ($task->category)
                                    <span style="color: {{ $task->category->color }}">{{ $task->category->name }}</span>
                                @endif
                                @if ($task->subtasks_count)
                                    <span>{{ $task->subtasks_completed_count }}/{{ $task->subtasks_count }} subtasks</span>
                                @endif
                                @foreach ($task->tags->take(2) as $tag)
                                    <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-500 dark:bg-slate-800 dark:text-slate-400">#{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        </div>

                        <div class="hidden shrink-0 items-center gap-2 sm:flex">
                            <x-priority-badge :priority="$task->priority" />
                            @if ($task->is_overdue)
                                <span class="badge-rose">Overdue</span>
                            @endif
                        </div>

                        <div class="shrink-0 opacity-0 transition group-hover:opacity-100">
                            @if ($status === 'archived')
                                <form action="{{ route('tasks.restore', $task) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn-ghost p-1.5" title="Restore">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('tasks.edit', $task) }}" class="btn-ghost p-1.5" title="Edit">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </a>
                            @endif
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Delete this task?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn-ghost p-1.5 text-rose-500" title="Delete">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="mt-4">
            {{ $tasks->withQueryString()->links() }}
        </div>
    @endif
</x-app-layout>
