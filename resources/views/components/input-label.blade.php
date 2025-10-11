@props(['value'])

@php
    $base = 'block font-medium text-sm text-gray-700 dark:text-gray-300 transition-colors duration-200 mb-1';
    $classes = $base;
    if ($attributes->get('floating'))
        $classes .= ' form-label';
@endphp

<label {{ $attributes->merge(['class' => $classes]) }}>
    {{ $value ?? $slot }}
    @if ($attributes->get('required'))
        <span class="text-red-500">*</span>
    @endif
</label>