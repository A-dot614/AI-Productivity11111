<x-app-layout>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Notifications</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                @if ($unreadCount > 0)
                    You have <strong class="font-semibold text-brand-600 dark:text-brand-400">{{ $unreadCount }} unread</strong> notification{{ $unreadCount === 1 ? '' : 's' }}.
                @else
                    You're all caught up.
                @endif
            </p>
        </div>
        @if ($notifications->isNotEmpty())
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button class="btn-secondary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    @if ($notifications->isEmpty())
        <x-empty-state icon="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
            title="No notifications" message="Task reminders, digests and AI insights will land here." />
    @else
        <div class="card overflow-hidden">
            <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach ($notifications as $notification)
                    <li class="flex items-start gap-3 px-4 py-4 transition hover:bg-slate-50 dark:hover:bg-slate-800/50 sm:px-5 {{ $notification->read_at ? 'opacity-60' : 'bg-brand-50/40 dark:bg-brand-500/5' }}">
                        <span class="mt-1.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $notification->read_at ? 'bg-slate-100 text-slate-400 dark:bg-slate-800' : 'bg-brand-100 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400' }}">
                            @if ($notification->type === 'reminder')
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                            @else
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            @endif
                        </span>

                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $notification->title }}</p>
                            <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">{{ $notification->body }}</p>
                            <p class="mt-1 text-xs text-slate-400 dark:text-slate-600">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                @csrf
                                @method('PATCH')
                                <button class="btn-ghost p-1.5" title="Mark as read">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('notifications.destroy', $notification) }}" onsubmit="return confirm('Delete this notification?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn-ghost p-1.5 text-rose-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</x-app-layout>
