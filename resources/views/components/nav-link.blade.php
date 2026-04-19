@props(['active', 'icon'])

@php
    $classes =
        $active ?? false
            ? 'flex items-center px-4 py-2.5 text-sm font-bold rounded-xl bg-brand-50 text-brand-600 transition duration-150 ease-in-out border border-brand-100/50'
            : 'flex items-center px-4 py-2.5 text-sm font-medium rounded-xl text-surface-500 hover:text-brand-600 hover:bg-brand-50 transition duration-150 ease-in-out border border-transparent';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <span class="mr-3 {{ $active ?? false ? 'text-brand-600' : 'text-surface-400' }}">
        <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
    </span>

    <span class="leading-none">{{ $slot }}</span>
</a>
