@php
    $base = 'inline-flex items-center justify-center font-semibold rounded-md '
        . 'transition-all duration-200 shadow-sm hover:shadow-md '
        . 'focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900';

    $color = 'bg-primary-600 hover:bg-primary-700 active:bg-primary-800 text-white';

    if ($attributes->get('gradient'))
        $color = 'btn-gradient text-white';
    if ($attributes->get('glass'))
        $color = 'btn-glass';
    if ($attributes->get('outline'))
        $color = 'btn-outline';
    if ($attributes->get('ripple'))
        $base .= ' btn-ripple';

    $size = 'px-4 py-2 text-xs uppercase tracking-widest';
    if ($attributes->get('sm'))
        $size = 'btn-sm';
    if ($attributes->get('lg'))
        $size = 'btn-lg';
    if ($attributes->get('xl'))
        $size = 'btn-xl';

    $classes = trim("$base $color $size");
    $loading = $attributes->get('loading');
@endphp

<button {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }} @if($loading) disabled @endif>
    @if($loading)
        <svg class="-ml-1 mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
        <span class="opacity-75 cursor-not-allowed">{{ $slot }}</span>
    @else
        {{ $slot }}
    @endif

</button>