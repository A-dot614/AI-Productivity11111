<section>
    <x-card title="Profile information" subtitle="Update your name, avatar and preferences.">
        <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="hidden">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-5" enctype="multipart/form-data">
            @csrf
            @method('patch')

            <div class="flex items-center gap-4">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-16 w-16 rounded-full object-cover ring-2 ring-brand-500/30">
                <div class="flex-1">
                    <label for="avatar" class="btn-secondary !py-2 text-xs cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        Change photo
                    </label>
                    <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden">
                    <p class="mt-1 text-xs text-slate-400">JPG or PNG, up to 2MB.</p>
                </div>
            </div>
            <x-input-error :messages="$errors->get('avatar')" class="mt-2" />

            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2 rounded-lg bg-amber-50 px-3 py-2 text-sm text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="font-semibold underline">{{ __('Click here to re-send the verification email.') }}</button>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="timezone" :value="__('Timezone')" />
                    <select id="timezone" name="timezone" class="input mt-1 w-full">
                        @foreach (timezone_identifiers_list() as $timezone)
                            <option value="{{ $timezone }}" @selected(old('timezone', $user->timezone) === $timezone)>{{ $timezone }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('timezone')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="productivity_goal_minutes" :value="__('Daily focus goal (minutes)')" />
                    <input type="number" id="productivity_goal_minutes" name="productivity_goal_minutes" min="30" max="1440" value="{{ old('productivity_goal_minutes', $user->productivity_goal_minutes) }}" class="input mt-1 w-full">
                    <x-input-error :messages="$errors->get('productivity_goal_minutes')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="theme" :value="__('Theme')" />
                <select id="theme" name="theme" class="input mt-1 w-full">
                    <option value="light" @selected(old('theme', $user->theme) === 'light')>Light</option>
                    <option value="dark" @selected(old('theme', $user->theme) === 'dark')>Dark</option>
                </select>
                <x-input-error :messages="$errors->get('theme')" class="mt-2" />
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="btn-primary">Save changes</button>

                @if (session('status') === 'profile-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm text-emerald-600 dark:text-emerald-400">Saved.</p>
                @endif
            </div>
        </form>
    </x-card>
</section>
