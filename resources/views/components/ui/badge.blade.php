@props([
    'variant' => 'surface', 
])

@php
    $variants = [
        'brand'   => 'bg-brand-50 text-brand-700 border-brand-100',
        'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
        'blue'    => 'bg-blue-50 text-blue-700 border-blue-100',
        'orange'  => 'bg-orange-50 text-orange-700 border-orange-100',
        'purple'  => 'bg-purple-50 text-purple-700 border-purple-100',
        'surface' => 'bg-surface-50 text-surface-600 border-surface-200',
    ];

    $classes = "inline-flex items-center px-1.5 py-0.5 rounded-full text-xxs font-bold border uppercase tracking-tight " . ($variants[$variant] ?? $variants['surface']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
