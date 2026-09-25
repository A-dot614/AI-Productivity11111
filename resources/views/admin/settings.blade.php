<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">System settings</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Defaults for reminders, digests and task views.</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-3xl">
        @csrf
        @method('PUT')
        <x-card title="Notifications">
            <div class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="reminder_lead_minutes" class="label">Reminder lead time (minutes)</label>
                        <input type="number" id="reminder_lead_minutes" name="reminder_lead_minutes" min="1" max="10080" value="{{ old('reminder_lead_minutes', $values['reminder_lead_minutes'] ?? '') }}" placeholder="30" class="input">
                    </div>
                    <div>
                        <label for="reminder_channel" class="label">Reminder channel</label>
                        <select id="reminder_channel" name="reminder_channel" class="input">
                            @foreach (['app' => 'In-app only', 'email' => 'Email only', 'both' => 'Both'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('reminder_channel', $values['reminder_channel'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="default_task_view" class="label">Default task view</label>
                    <select id="default_task_view" name="default_task_view" class="input">
                        @foreach (['list' => 'List', 'board' => 'Board', 'calendar' => 'Calendar'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('default_task_view', $values['default_task_view'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-card>

        <x-card title="Daily digest" class="mt-6">
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="daily_digest_enabled" name="daily_digest_enabled" value="1"
                        @checked(old('daily_digest_enabled', (bool) ($values['daily_digest_enabled'] ?? false)))
                        class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">
                    <label for="daily_digest_enabled" class="text-sm text-slate-600 dark:text-slate-300">Send a daily email digest</label>
                </div>
                <div>
                    <label for="daily_digest_time" class="label">Send time</label>
                    <input type="time" id="daily_digest_time" name="daily_digest_time" value="{{ old('daily_digest_time', $values['daily_digest_time'] ?? '') }}" class="input">
                </div>
            </div>
        </x-card>

        <div class="mt-6">
            <button type="submit" class="btn-primary">Save settings</button>
        </div>
    </form>
</x-app-layout>
