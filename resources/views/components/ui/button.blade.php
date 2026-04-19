@props([
    'variant' => 'primary', 
    'icon' => null,
    'size' => 'md', // md, sm, icon
])

@php
    $variants = [
        'primary'   => 'bg-brand-800 hover:bg-brand-900 text-white shadow-sm',
        'secondary' => 'bg-white border border-surface-200 text-surface-600 hover:bg-surface-50 shadow-sm',
        'danger'    => 'bg-red-50 text-red-600 hover:bg-red-100 border border-red-100',
        'ghost'     => 'text-surface-400 hover:text-brand-600 hover:bg-brand-50',
    ];

    $sizes = [
        'md' => 'px-4 py-1.5',
        'sm' => 'px-3 py-1',
        'icon' => 'p-1.5',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $classes = "inline-flex items-center justify-center rounded-lg text-xs font-bold transition-all duration-200 " . $sizeClass . " " . ($variants[$variant] ?? $variants['primary']);
@endphp

<button {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <i data-lucide="{{ $icon }}" class="w-4 h-4 {{ $slot->isEmpty() ? '' : 'mr-2' }}"></i>
    @endif
    {{ $slot }}
</button>
