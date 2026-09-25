<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">AI logs</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Every AI request made across the platform.</p>
    </div>

    <form method="GET" action="{{ route('admin.logs.index') }}" class="card mb-6 flex flex-wrap items-end gap-3 p-4">
        <div>
            <label class="label">Feature</label>
            <select name="feature" class="input">
                <option value="">All</option>
                @foreach ($features as $feature)
                    <option value="{{ $feature }}" @selected($featureFilter === $feature)>{{ str_replace('_', ' ', $feature) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">Status</label>
            <select name="status" class="input">
                <option value="">All</option>
                <option value="failed" @selected($statusFilter === 'failed')>Failed</option>
            </select>
        </div>
        <button class="btn-primary !py-2.5">Filter</button>
    </form>

    @if ($logs->isEmpty())
        <x-empty-state title="No AI logs" message="Requests appear here once users use the assistant." />
    @else
        <div class="card overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-slate-100 text-xs uppercase tracking-wide text-slate-400 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3">User</th>
                        <th class="hidden px-5 py-3 sm:table-cell">Feature</th>
                        <th class="hidden px-5 py-3 md:table-cell">Provider</th>
                        <th class="hidden px-5 py-3 lg:table-cell">Status</th>
                        <th class="hidden px-5 py-3 lg:table-cell">Latency</th>
                        <th class="px-5 py-3">When</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($logs as $log)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="px-5 py-3">
                                <span class="font-medium text-slate-700 dark:text-slate-200">{{ $log->user?->name ?? 'Deleted user' }}</span>
                            </td>
                            <td class="hidden px-5 py-3 text-slate-500 sm:table-cell">{{ str_replace('_', ' ', $log->feature) }}</td>
                            <td class="hidden px-5 py-3 text-slate-500 md:table-cell">{{ $log->provider }}</td>
                            <td class="hidden px-5 py-3 lg:table-cell">
                                @if ($log->status === 'success')
                                    <span class="badge-green">Success</span>
                                @elseif ($log->status === 'failed')
                                    <span class="badge-rose" title="{{ $log->error_message }}">Failed</span>
                                @else
                                    <span class="badge-slate">{{ $log->status }}</span>
                                @endif
                            </td>
                            <td class="hidden px-5 py-3 text-slate-500 lg:table-cell">{{ $log->duration_ms }} ms</td>
                            <td class="px-5 py-3 text-slate-400">{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $logs->withQueryString()->links() }}
        </div>
    @endif
</x-app-layout>
