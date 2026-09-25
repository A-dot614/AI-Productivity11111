<x-guest-layout>
    <x-auth-card title="Set a new password">
        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <button type="submit" class="btn-primary w-full">{{ __('Reset password') }}</button>
        </form>
    </x-auth-card>
</x-guest-layout>
