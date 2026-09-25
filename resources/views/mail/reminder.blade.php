<x-mail::message>
# Task reminder

Hi **{{ $reminder->user->name }}**,

Here's a quick reminder for your upcoming task:

**{{ $reminder->subject }}**

@if ($reminder->remindable?->due_date)
**Due:** {{ $reminder->remindable->due_date->format('l, F j, Y \a\t g:i A') }}
@else
**Due:** {{ $reminder->remind_at->format('l, F j, Y \a\t g:i A') }}
@endif

@if ($reminder->remindable && $reminder->remindable->category)
**Category:** {{ $reminder->remindable->category->name }}
@endif

<x-mail::button :url="route('tasks.index', ['due' => 'today'])">
Open your tasks
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
