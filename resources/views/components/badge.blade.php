@props(['type' => 'info'])

@php
    $classes = [
        'success' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20',
        'danger' => 'bg-red-50 text-red-700 ring-1 ring-red-600/20',
        'warning' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20',
        'info' => 'bg-sky-50 text-sky-700 ring-1 ring-sky-600/20',
        'gray' => 'bg-gray-50 text-gray-700 ring-1 ring-gray-600/20',
    ][$type] ?? 'bg-gray-50 text-gray-700 ring-1 ring-gray-600/20';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold $classes"]) }}>
    {{ $slot }}
</span>
