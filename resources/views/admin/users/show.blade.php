<x-app-layout>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}" class="btn-ghost p-2">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
            </a>
            <div class="flex items-center gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-100 text-lg font-bold text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </span>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $user->name }}</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $user->email }} · joined {{ $user->created_at->format('M j, Y') }}</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.role', $user) }}" class="flex items-center gap-2" x-data>
            @csrf
            @method('PATCH')
            <select name="role" class="input !w-auto !py-2 text-sm" onchange="this.form.submit()">
                <option value="user" @selected($user->role === 'user')>User</option>
                <option value="admin" @selected($user->role === 'admin')>Admin</option>
            </select>
        </form>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Tasks" value="{{ $user->tasks_count }}" tone="indigo"
            icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        <x-stat-card label="Notes" value="{{ $user->notes_count }}" tone="violet"
            icon="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        <x-stat-card label="Calendar events" value="{{ $user->calendar_events_count }}" tone="sky"
            icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        <x-stat-card label="AI requests" value="{{ $user->ai_histories_count }}" tone="amber"
            icon="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707" />
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
        <x-card title="Recent tasks">
            @if ($recentTasks->isEmpty())
                <p class="text-sm text-slate-400">This user has no tasks yet.</p>
            @else
                <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($recentTasks as $task)
                        <li class="flex items-center gap-3 py-2.5">
                            <span class="h-2 w-2 shrink-0 rounded-full {{ $task->status === 'completed' ? 'bg-emerald-500' : ($task->status === 'in_progress' ? 'bg-brand-500' : 'bg-slate-300 dark:bg-slate-600') }}"></span>
                            <span class="min-w-0 flex-1 truncate text-sm text-slate-700 dark:text-slate-200">{{ $task->title }}</span>
                            <span class="text-xs text-slate-400">{{ $task->due_date?->format('M j') ?? '—' }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>

        <x-card title="Danger zone">
            <p class="text-sm text-slate-500 dark:text-slate-400">Permanently remove this user and all of their data. This action cannot be undone.</p>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-4" onsubmit="return confirm('Permanently delete {{ $user->name }} and all their data?')">
                @csrf
                @method('DELETE')
                <button class="btn-danger">Delete user</button>
            </form>
        </x-card>
    </div>
</x-app-layout>
