<x-guest-layout>
    <x-auth-card title="Welcome back" subtitle="Log in to your workspace">
        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-brand-600 shadow-sm focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800" name="remember">
                    <span class="ms-2 text-sm text-slate-600 dark:text-slate-300">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm font-medium text-brand-600 hover:underline dark:text-brand-400" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <button type="submit" class="btn-primary w-full">{{ __('Log in') }}</button>
        </form>
    </x-auth-card>
</x-guest-layout>
