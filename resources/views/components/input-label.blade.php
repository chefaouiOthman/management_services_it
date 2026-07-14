@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-[#475569]']) }}>
    {{ $value ?? $slot }}
</label>
