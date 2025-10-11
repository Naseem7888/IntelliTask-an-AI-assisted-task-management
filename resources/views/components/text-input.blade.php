@props(['disabled' => false])

@php
    // Build classes. For glass inputs, avoid default bg/text utilities so custom glass styles win.
    $classes = 'rounded-md shadow-sm transition-colors duration-200';

    if ($attributes->has('glass')) {
        // Glass style handles background, text color, borders, and focus visuals
        $classes .= ' form-input-glass';
    } else {
        // Standard input styling (Tailwind utilities)
        $classes .= ' text-gray-900 dark:text-white bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600 focus:border-primary-500 focus:ring-primary-500 dark:focus:border-primary-400';
    }

    if ($attributes->has('floating'))
        $classes .= ' form-input-floating';
    if ($attributes->get('icon') === 'left')
        $classes .= ' form-input-icon-left';
    if ($attributes->get('icon') === 'right')
        $classes .= ' form-input-icon-right';
    if ($attributes->has('success'))
        $classes .= ' form-input-success';
    if ($attributes->has('error') || $attributes->get('aria-invalid'))
        $classes .= ' form-input-error';
@endphp

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => $classes]) !!}>