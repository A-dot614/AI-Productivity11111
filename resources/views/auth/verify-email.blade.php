<x-guest-layout>
    <x-auth-card title="Verify your email" subtitle="One more step">
        <div class="mb-4 text-sm text-slate-500 dark:text-slate-400">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 rounded-lg bg-emerald-50 px-3 py-2 text-sm text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn-secondary">{{ __('Resend verification email') }}</button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-ghost text-sm">{{ __('Log out') }}</button>
            </form>
        </div>
    </x-auth-card>
</x-guest-layout>
