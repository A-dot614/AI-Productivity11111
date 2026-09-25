<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">New note</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Write in markdown. Ask AI to summarize later.</p>
        </div>
        <a href="{{ route('notes.index') }}" class="btn-ghost text-sm">Back to notes</a>
    </div>

    <form method="POST" action="{{ route('notes.store') }}">
        @csrf
        <x-card>
            <div class="space-y-4">
                <div>
                    <label for="title" class="label">Title <span class="text-rose-500">*</span></label>
                    <input type="text" id="title" name="title" required value="{{ old('title') }}" placeholder="e.g. Meeting notes — Project sync"
                        class="input @error('title') input-error @enderror">
                    @error('title') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="label">Content</label>
                    <x-markdown-editor name="content" :value="old('content')" />
                    @error('content') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="is_pinned" name="is_pinned" value="1" @checked(old('is_pinned')) class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">
                    <label for="is_pinned" class="text-sm text-slate-600 dark:text-slate-300">Pin this note</label>
                </div>
            </div>
        </x-card>

        <div class="mt-6 flex items-center justify-end gap-3">
            <a href="{{ route('notes.index') }}" class="btn-ghost">Cancel</a>
            <button type="submit" class="btn-primary">Save note</button>
        </div>
    </form>
</x-app-layout>
