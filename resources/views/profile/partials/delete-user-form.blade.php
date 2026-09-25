<section>
    <x-card title="Danger zone" subtitle="Once deleted, all data is permanently removed." class="border-rose-200 dark:border-rose-500/20">
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Deleting your account removes your tasks, notes, calendar events and AI history. This cannot be undone.
        </p>

        <button type="button" class="btn-danger mt-4" @click="window.dispatchEvent(new CustomEvent('open-modal'))">
            Delete account
        </button>
    </x-card>

    <x-app-modal title="Confirm account deletion" x-init="if ({{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }}) window.dispatchEvent(new CustomEvent('open-modal'))">
        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
            @csrf
            @method('delete')

            <p class="text-sm text-slate-500 dark:text-slate-400">
                Enter your password to confirm you want to permanently delete your account.
            </p>

            <div>
                <label for="password" class="label">Password</label>
                <input id="password" name="password" type="password" placeholder="Your password" class="input">
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" @click="window.dispatchEvent(new CustomEvent('close-modal'))" class="btn-ghost">Cancel</button>
                <button type="submit" class="btn-danger">Permanently delete</button>
            </div>
        </form>
    </x-app-modal>
</section>
