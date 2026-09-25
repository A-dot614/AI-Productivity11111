<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('notes.index') }}" class="btn-ghost p-2">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $note->title }}</h1>
                <p class="mt-1 text-xs text-slate-400">Updated {{ $note->updated_at->diffForHumans() }}</p>
            </div>
        </div>
        <div class="flex shrink-0 items-center gap-2">
            <form action="{{ route('notes.pin', $note) }}" method="POST">
                @csrf
                @method('PATCH')
                <button class="btn-ghost p-2" title="{{ $note->is_pinned ? 'Unpin' : 'Pin' }}">
                    <svg class="h-5 w-5 {{ $note->is_pinned ? 'text-amber-500' : 'text-slate-400' }}" :fill="{{ $note->is_pinned ? "'currentColor'" : "'none'" }}" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 3a1 1 0 011-1h10a1 1 0 011 1v1.586c0 .265-.105.52-.293.707L12.414 7.586A2 2 0 0011 8.828V16l-2 2v-9.172a2 2 0 00-.414-1.242L3.293 4.293A1 1 0 003 3.586V3z" /></svg>
                </button>
            </form>
            <a href="{{ route('notes.edit', $note) }}" class="btn-secondary">Edit</a>
            <form action="{{ route('notes.destroy', $note) }}" method="POST" onsubmit="return confirm('Move this note to trash?')">
                @csrf
                @method('DELETE')
                <button class="btn-ghost p-2 text-rose-500">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
            </form>
        </div>
    </div>

    <x-card :padding="false">
        <div class="markdown-body px-6 py-5 sm:px-8">
            {!! app(\App\Services\Markdown\MarkdownService::class)->render($note->content ?? '') !!}
        </div>
    </x-card>

    @if ($note->task)
        <div class="mt-6 rounded-xl border border-slate-200 p-4 text-sm dark:border-slate-800">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Linked to task</p>
            <a href="{{ route('tasks.show', $note->task) }}" class="mt-1 block font-medium text-brand-600 hover:underline dark:text-brand-400">{{ $note->task->title }}</a>
        </div>
    @endif
</x-app-layout>
