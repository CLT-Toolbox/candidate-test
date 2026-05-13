@props(['active'])

@php
$classes = ($active ?? false)
            ? 'nav-tab-active block w-full text-start'
            : 'nav-tab block w-full text-start';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
