@props(['status' => 'pending'])

@php
    $config = [
        'pending' => ['label' => 'Pending', 'class' => 'badge-slate'],
        'in_progress' => ['label' => 'In progress', 'class' => 'badge-indigo'],
        'completed' => ['label' => 'Completed', 'class' => 'badge-green'],
        'archived' => ['label' => 'Archived', 'class' => 'badge-slate'],
    ][$status] ?? ['label' => $status, 'class' => 'badge-slate'];
@endphp

<span {{ $attributes->merge(['class' => $config['class']]) }}>
    {{ $config['label'] }}
</span>
