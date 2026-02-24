@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center px-4 pt-1 border-b-2 border-[#3f7a5c] text-sm font-semibold leading-5 text-[#3f7a5c] focus:outline-none focus:border-[#3f7a5c] transition duration-150 ease-in-out'
    : 'inline-flex items-center px-4 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
