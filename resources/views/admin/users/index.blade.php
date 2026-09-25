<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Users</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $users->total() }} registered account(s).</p>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="card mb-6 flex flex-wrap items-end gap-3 p-4">
        <div class="flex-1">
            <label class="label">Search</label>
            <input type="text" name="q" value="{{ $search }}" placeholder="Name or email…" class="input">
        </div>
        <div>
            <label class="label">Role</label>
            <select name="role" class="input">
                <option value="">All</option>
                <option value="admin" @selected($roleFilter === 'admin')>Admin</option>
                <option value="user" @selected($roleFilter === 'user')>User</option>
            </select>
        </div>
        <button class="btn-primary !py-2.5">Filter</button>
    </form>

    @if ($users->isEmpty())
        <x-empty-state title="No users found" message="Adjust your search or filters." />
    @else
        <div class="card overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-slate-100 text-xs uppercase tracking-wide text-slate-400 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3">User</th>
                        <th class="hidden px-5 py-3 sm:table-cell">Role</th>
                        <th class="hidden px-5 py-3 md:table-cell">Tasks</th>
                        <th class="hidden px-5 py-3 md:table-cell">Notes</th>
                        <th class="hidden px-5 py-3 lg:table-cell">Joined</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($users as $user)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="px-5 py-3">
                                <a href="{{ route('admin.users.show', $user) }}" class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                    <span>
                                        <span class="block font-medium text-slate-800 dark:text-slate-100">{{ $user->name }}</span>
                                        <span class="block text-xs text-slate-400">{{ $user->email }}</span>
                                    </span>
                                </a>
                            </td>
                            <td class="hidden px-5 py-3 sm:table-cell">
                                <span class="badge {{ $user->role === 'admin' ? 'badge-violet' : 'badge-slate' }}">{{ $user->role }}</span>
                            </td>
                            <td class="hidden px-5 py-3 text-slate-500 md:table-cell">{{ $user->tasks_count }}</td>
                            <td class="hidden px-5 py-3 text-slate-500 md:table-cell">{{ $user->notes_count }}</td>
                            <td class="hidden px-5 py-3 text-slate-500 lg:table-cell">{{ $user->created_at->format('M j, Y') }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn-ghost px-3 py-1.5 text-xs">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->withQueryString()->links() }}
        </div>
    @endif
</x-app-layout>
