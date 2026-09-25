<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Global categories</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Available to every user out of the box.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            @if ($categories->isEmpty())
                <x-empty-state title="No global categories" message="Create one to give every user a starting point." />
            @else
                <div class="card overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-slate-100 text-xs uppercase tracking-wide text-slate-400 dark:border-slate-800">
                            <tr>
                                <th class="px-5 py-3">Category</th>
                                <th class="hidden px-5 py-3 sm:table-cell">Tasks</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($categories as $category)
                                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-9 w-9 items-center justify-center rounded-lg text-white" style="background: {{ $category->color }}">{{ $category->icon ?: strtoupper(substr($category->name, 0, 1)) }}</span>
                                            <span class="font-medium text-slate-700 dark:text-slate-200">{{ $category->name }}</span>
                                        </div>
                                    </td>
                                    <td class="hidden px-5 py-3 text-slate-500 sm:table-cell">{{ $category->tasks_count }}</td>
                                    <td class="px-5 py-3">
                                        <div class="flex justify-end gap-1">
                                            <button type="button" class="btn-ghost p-1.5" title="Edit"
                                                x-data x-on:click="window.dispatchEvent(new CustomEvent('open-modal')); window.dispatchEvent(new CustomEvent('edit-category', { detail: { id: '{{ $category->id }}', name: '{{ $category->name }}', color: '{{ $category->color }}', icon: '{{ $category->icon }}' } }))">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            </button>
                                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this global category?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn-ghost p-1.5 text-rose-500">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $categories->links() }}</div>
            @endif
        </div>

        <div>
            <x-card title="New global category">
                <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="label">Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="e.g. Work" class="input">
                    </div>
                    <div>
                        <label class="label">Color</label>
                        <input type="color" name="color" value="#6366f1" class="h-10 w-full cursor-pointer rounded-lg border border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-800">
                    </div>
                    <div>
                        <label class="label">Icon (emoji)</label>
                        <input type="text" name="icon" maxlength="32" placeholder="💼" class="input">
                    </div>
                    <button type="submit" class="btn-primary w-full">Add category</button>
                </form>
            </x-card>
        </div>
    </div>

    <x-app-modal title="Edit global category">
        <div x-data="appModalEdit()" x-on:edit-category.window="edit($event.detail)">
            <template x-if="editing">
                <form :action="'{{ route('admin.categories.update', '__ID__') }}'.replace('__ID__', editing.id)" method="POST" class="space-y-4">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PATCH">
                    <div>
                        <label class="label">Name</label>
                        <input type="text" name="name" x-model="editing.name" required class="input">
                    </div>
                    <div>
                        <label class="label">Color</label>
                        <input type="color" name="color" x-model="editing.color" class="h-10 w-full cursor-pointer rounded-lg border border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-800">
                    </div>
                    <div>
                        <label class="label">Icon</label>
                        <input type="text" name="icon" x-model="editing.icon" class="input">
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
