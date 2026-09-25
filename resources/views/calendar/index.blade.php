<x-app-layout>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Calendar</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tasks (by priority color) and events in one view. Drag to reschedule.</p>
        </div>
        <button type="button" class="btn-primary" onclick="window.dispatchEvent(new CustomEvent('open-create-event-modal'))">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            New event
        </button>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400"><span class="h-3 w-3 rounded-full" style="background: #ef4444"></span> Urgent task</div>
        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400"><span class="h-3 w-3 rounded-full" style="background: #f59e0b"></span> High</div>
        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400"><span class="h-3 w-3 rounded-full" style="background: #6366f1"></span> Medium</div>
        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400"><span class="h-3 w-3 rounded-full" style="background: #10b981"></span> Low / event</div>
    </div>

    <div class="mt-4">
        <div id="calendar" class="card p-4"
            data-events-url="{{ route('calendar.data') }}"
            data-task-url="{{ route('tasks.show', '__ID__') }}"
            data-reschedule-url="{{ route('tasks.reschedule', '__ID__') }}"
            data-move-url="{{ route('events.move', '__ID__') }}"
            data-event-delete-url="{{ route('events.destroy', '__ID__') }}"
            data-token="{{ csrf_token() }}"></div>
    </div>

    {{-- Create event modal --}}
    <div id="event-modal">
        <x-app-modal title="Add event" open-event="open-create-event-modal">
            <form method="POST" action="{{ route('events.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="event_title" class="label">Title <span class="text-rose-500">*</span></label>
                    <input type="text" id="event_title" name="title" required placeholder="e.g. Meeting with supervisor" class="input">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="event_start_at" class="label">Starts <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" id="event_start_at" name="start_at" required class="input">
                    </div>
                    <div>
                        <label for="event_end_at" class="label">Ends</label>
                        <input type="datetime-local" id="event_end_at" name="end_at" class="input">
                    </div>
                </div>
                <div>
                    <label for="event_description" class="label">Description</label>
                    <textarea id="event_description" name="description" rows="3" class="input"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="event_all_day" name="is_all_day" value="1" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">
                    <label for="event_all_day" class="text-sm text-slate-600 dark:text-slate-300">All day</label>
                </div>
                <div>
                    <label for="event_color" class="label">Color</label>
                    <input type="color" id="event_color" name="color" value="#6366f1" class="h-10 w-full cursor-pointer rounded-lg border border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-800">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="window.dispatchEvent(new CustomEvent('close-modal'))" class="btn-ghost">Cancel</button>
                    <button type="submit" class="btn-primary">Save event</button>
                </div>
            </form>
        </x-app-modal>
    </div>

    {{-- Edit event modal --}}
    <div id="event-edit-modal">
        <x-app-modal title="Edit event" open-event="open-edit-event-modal">
            <div x-data="eventEdit()" x-on:edit-event.window="load($event.detail)">
                <template x-if="editing">
                    <div>
                        <form :action="editing.delete_url" method="POST" @submit.prevent="if (confirm('Delete this event?')) $el.submit()" class="mb-4 text-right">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn-ghost text-rose-500">
                                <svg class="mr-1 inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                Delete
                            </button>
                        </form>
                    <form :action="'{{ route('events.update', '__ID__') }}'.replace('__ID__', editing.id)" method="POST" class="space-y-4">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <div>
                            <label class="label">Title</label>
                            <input type="text" name="title" x-model="editing.title" required class="input">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Starts</label>
                                <input type="datetime-local" name="start_at" x-model="editing.start_at" class="input">
                            </div>
                            <div>
                                <label class="label">Ends</label>
                                <input type="datetime-local" name="end_at" x-model="editing.end_at" class="input">
                            </div>
                        </div>
                        <div>
                            <label class="label">Description</label>
                            <textarea name="description" rows="3" x-model="editing.description" class="input"></textarea>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_all_day" value="1" x-model="editing.all_day" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">
                            <label class="text-sm text-slate-600 dark:text-slate-300">All day</label>
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
                    </div>
                </template>
            </div>
        </x-app-modal>
    </div>

    @push('scripts')
    @vite('resources/js/calendar.js')
    <script>
        window.eventEdit = function () {
            return {
                editing: null,
                load(data) {
                    this.editing = {
                        ...data,
                        all_day: data.all_day,
                    };
                },
            };
        };
    </script>
    @endpush
</x-app-layout>
