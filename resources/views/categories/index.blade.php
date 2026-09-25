<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Categories</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Group tasks so you can filter and analyze by area of focus.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- List --}}
        <div class="lg:col-span-2">
            @if ($categories->isEmpty())
                @php $addCategoryButton = '<button type="button" onclick="window.dispatchEvent(new CustomEvent(\'open-modal\'))" class="btn-primary !py-2 text-xs">Add category</button>'; @endphp
                <x-empty-state icon="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                    title="No categories yet" message="Create your first category to organize your tasks."
                    :action="$addCategoryButton" />
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ($categories as $category)
                        <div class="card group flex items-center gap-4 p-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-lg font-bold text-white" style="background: {{ $category->color }}">
                                {{ strtoupper(substr($category->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $category->name }}</p>
                                <p class="text-xs text-slate-400">{{ $category->tasks_count }} task{{ $category->tasks_count === 1 ? '' : 's' }}</p>
                            </div>
                            @unless ($category->is_global)
                                <div class="flex shrink-0 gap-1 opacity-0 transition group-hover:opacity-100">
                                    <button type="button" class="btn-ghost p-1.5" title="Edit"
                                        onclick="window.dispatchEvent(new CustomEvent('open-modal'))"
                                        x-data x-on:click="$dispatch('edit-category', { id: '{{ $category->id }}', name: '{{ $category->name }}', color: '{{ $category->color }}', icon: '{{ $category->icon }}' })">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category? Tasks keep their data but lose the grouping.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-ghost p-1.5 text-rose-500" title="Delete">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            @endunless
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Create form --}}
        <div>
            <x-card title="New category">
                <form method="POST" action="{{ route('categories.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="label">Name <span class="text-rose-500">*</span></label>
                        <input type="text" id="name" name="name" required placeholder="e.g. University" class="input @error('name') input-error @enderror">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="color" class="label">Color</label>
                        <input type="color" id="color" name="color" value="#6366f1" class="h-10 w-full cursor-pointer rounded-lg border border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-800">
                    </div>
                    <div>
                        <label for="icon" class="label">Icon (emoji)</label>
                        <input type="text" id="icon" name="icon" maxlength="2" placeholder="📚" class="input">
                    </div>
                    <button type="submit" class="btn-primary w-full">Add category</button>
                </form>
            </x-card>
        </div>
    </div>

    {{-- Edit modal --}}
    <x-app-modal title="Edit category">
        <div x-data="appModalEdit()" x-on:edit-category.window="edit($event.detail)">
            <template x-if="editing">
                <form :action="'/categories/' + editing.id" method="POST" class="space-y-4">
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
                    <div>
                        <label class="label">Icon (emoji)</label>
                        <input type="text" name="icon" x-model="editing.icon" maxlength="2" class="input">
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
