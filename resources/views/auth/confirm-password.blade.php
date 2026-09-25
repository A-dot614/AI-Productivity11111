<x-guest-layout>
    <x-auth-card title="Confirm your password" subtitle="This is a secure area">
        <div class="mb-4 text-sm text-slate-500 dark:text-slate-400">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <button type="submit" class="btn-primary w-full">{{ __('Confirm') }}</button>
        </form>
    </x-auth-card>
</x-guest-layout>
