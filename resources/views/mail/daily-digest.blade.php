<x-mail::message>
# Good morning, {{ $user->name }}!

Here is your personalized plan for today.

<x-mail::panel>
{!! nl2br(e($digest)) !!}
</x-mail::panel>

<x-mail::button :url="route('dashboard')">
Go to your dashboard
</x-mail::button>

Have a productive day,<br>
{{ config('app.name') }}
</x-mail::message>
