@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'mt-1 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li
                class="flex items-start text-sm animate-shake bg-red-50 dark:bg-red-900/20 border-l-2 border-red-500 p-2 pl-3 rounded-r">
                <svg class="h-4 w-4 text-red-500 mr-1.5 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-red-600 dark:text-red-400">{{ $message }}</span>
            </li>
        @endforeach
    </ul>
@endif