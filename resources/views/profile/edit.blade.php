<x-app-layout>
    @php $title = 'Profile'; @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Profile settings</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage your account, preferences and security.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            @include('profile.partials.update-profile-information-form')
            @include('profile.partials.update-password-form')
        </div>
        <div class="space-y-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
