<x-guest-layout>
    <x-auth-card title="Reset password" subtitle="We'll email you a reset link">
        <div class="mb-4 text-sm text-slate-500 dark:text-slate-400">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <button type="submit" class="btn-primary w-full">{{ __('Email password reset link') }}</button>
        </form>
    </x-auth-card>
</x-guest-layout>
