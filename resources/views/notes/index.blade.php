<x-app-layout>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Notes</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Your knowledge base, in markdown.</p>
        </div>
        <a href="{{ route('notes.create') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            New note
        </a>
    </div>

    <form method="GET" action="{{ route('notes.index') }}" class="mb-6">
        <div class="relative">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input type="text" name="q" value="{{ $search }}" placeholder="Search notes…" class="input w-full pl-9 sm:w-96">
        </div>
    </form>

    @if ($notes->isEmpty())
        @php $createNoteLink = '<a href="'.route('notes.create').'" class="btn-primary !py-2 text-xs">Create your first note</a>'; @endphp
        <x-empty-state icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
            title="{{ $search ? 'No notes match your search' : 'No notes yet' }}"
            message="Capture ideas, meeting minutes, or lecture summaries with markdown support."
            :action="$createNoteLink" />
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($notes as $note)
                <a href="{{ route('notes.show', $note) }}"
                    class="card group flex flex-col p-5 transition-shadow hover:shadow-float">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="line-clamp-2 text-sm font-semibold text-slate-800 transition group-hover:text-brand-600 dark:text-slate-100 dark:group-hover:text-brand-400">{{ $note->title }}</h3>
                        @if ($note->is_pinned)
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a1 1 0 011-1h10a1 1 0 011 1v1.586c0 .265-.105.52-.293.707L12.414 7.586A2 2 0 0011 8.828V16l-2 2v-9.172a2 2 0 00-.414-1.242L3.293 4.293A1 1 0 003 3.586V3z" /></svg>
                        @endif
                    </div>
                    @if ($note->excerpt)
                        <p class="mt-2 line-clamp-3 flex-1 text-sm text-slate-500 dark:text-slate-400">{{ $note->excerpt }}</p>
                    @endif
                    <div class="mt-4 flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        {{ $note->updated_at->diffForHumans() }}
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $notes->withQueryString()->links() }}
        </div>
    @endif
</x-app-layout>
