@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'flex items-center w-full ps-4 pe-4 py-3 border-l-4 border-brand-600 text-start text-sm font-bold text-brand-700 bg-brand-50/50 focus:outline-none transition duration-150 ease-in-out'
            : 'flex items-center w-full ps-4 pe-4 py-3 border-l-4 border-transparent text-start text-sm font-medium text-surface-600 hover:text-brand-600 hover:bg-surface-50 hover:border-surface-300 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if(isset($icon))
        <span class="mr-4 {{ ($active ?? false) ? 'text-brand-600' : 'text-surface-400' }}">
            <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
        </span>
    @endif

    <span class="flex-1">{{ $slot }}</span>
</a>
