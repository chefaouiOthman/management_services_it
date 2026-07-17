@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-200 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:ring-offset-1 rounded-lg shadow-sm transition-all duration-200']) !!}>
