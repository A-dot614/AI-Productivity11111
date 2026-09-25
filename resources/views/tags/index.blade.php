<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Tags</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Lightweight labels that cut across categories — e.g. #urgent, #meeting, #idea.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            @if ($tags->isEmpty())
                @php $addTagButton = '<button type="button" onclick="window.dispatchEvent(new CustomEvent(\'open-modal\'))" class="btn-primary !py-2 text-xs">Add tag</button>'; @endphp
                <x-empty-state icon="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                    title="No tags yet" message="Create tags to label tasks across categories."
                    :action="$addTagButton" />
            @else
                <div class="flex flex-wrap gap-3">
                    @foreach ($tags as $tag)
                        <div class="group flex items-center gap-2 rounded-xl border px-3 py-2 transition hover:shadow-float"
                            style="border-color: {{ $tag->color }}55; background: {{ $tag->color }}11">
                            <span class="h-2.5 w-2.5 rounded-full" style="background: {{ $tag->color }}"></span>
                            <span class="text-sm font-medium" style="color: {{ $tag->color }}">#{{ $tag->name }}</span>
                            <span class="text-xs text-slate-400">{{ $tag->tasks_count }}</span>
                            <div class="ml-1 flex gap-1 opacity-0 transition group-hover:opacity-100">
                                <button type="button" class="btn-ghost p-1" title="Edit"
                                    x-data x-on:click="window.dispatchEvent(new CustomEvent('open-modal')); window.dispatchEvent(new CustomEvent('edit-tag', { detail: { id: '{{ $tag->id }}', name: '{{ $tag->name }}', color: '{{ $tag->color }}' } }))">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <form action="{{ route('tags.destroy', $tag) }}" method="POST" onsubmit="return confirm('Delete this tag?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-ghost p-1 text-rose-500">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <x-card title="New tag">
                <form method="POST" action="{{ route('tags.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="label">Name <span class="text-rose-500">*</span></label>
                        <input type="text" id="name" name="name" required placeholder="e.g. thesis" class="input @error('name') input-error @enderror">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="color" class="label">Color</label>
                        <input type="color" id="color" name="color" value="#10b981" class="h-10 w-full cursor-pointer rounded-lg border border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-800">
                    </div>
                    <button type="submit" class="btn-primary w-full">Add tag</button>
                </form>
            </x-card>
        </div>
    </div>

    <x-app-modal title="Edit tag">
        <div x-data="appModalEdit()" x-on:edit-tag.window="edit($event.detail)">
            <template x-if="editing">
                <form :action="'/tags/' + editing.id" method="POST" class="space-y-4">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <div>
                        <label class="label">Name</label>
                        <input type="text" name="name" x-model="editing.name" required class="input">
                    </div>
                    <div>
                        <label class="label">Color</label>
                        <input type="color" name="color" x-model="editing.color" class="h-10 w-full cursor-pointer rounded-lg border border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-800">
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="window.dispatchEvent(new CustomEvent('close-modal'))" class="btn-ghost">Cancel</button>
                        <button type="submit" class="btn-primary">Save</button>
                    </div>
                </form>
            </template>
        </div>
    </x-app-modal>

    @push('scripts')
    <script>
        window.appModalEdit = function () {
            return {
                editing: null,
                edit(data) {
                    this.editing = data;
                },
            };
        };
    </script>
    @endpush
</x-app-layout>
