@props(['priority' => 'medium'])

@php
    $config = [
        'urgent' => ['label' => 'Urgent', 'class' => 'badge-rose'],
        'high' => ['label' => 'High', 'class' => 'badge-amber'],
        'medium' => ['label' => 'Medium', 'class' => 'badge-indigo'],
        'low' => ['label' => 'Low', 'class' => 'badge-slate'],
    ][$priority] ?? ['label' => 'Medium', 'class' => 'badge-indigo'];
@endphp

<span {{ $attributes->merge(['class' => $config['class']]) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
    {{ $config['label'] }}
</span>
