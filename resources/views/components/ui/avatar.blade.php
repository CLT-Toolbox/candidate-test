@props([
    'name',
    'size' => 'md', // sm, md, lg
])

@php
    $initials = collect(explode(' ', $name))
        ->map(fn($segment) => substr($segment, 0, 1))
        ->take(2)
        ->join('');

    $variants = [
        'bg-blue-50 text-blue-600',
        'bg-emerald-50 text-emerald-600',
        'bg-orange-50 text-orange-600',
        'bg-purple-50 text-purple-600',
    ];

    // Simple hash to get consistent color for the same name
    $colorIndex = abs(crc32($name)) % count($variants);
    $colorClass = $variants[$colorIndex];

    $sizes = [
        'sm' => 'w-6 h-6 text-xxs',
        'md' => 'w-8 h-8 text-xs',
        'lg' => 'w-10 h-10 text-sm',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div {{ $attributes->merge(['class' => "$sizeClass rounded-lg $colorClass flex items-center justify-center font-bold ring-1 ring-surface-950/10 shadow-sm"]) }}>
    {{ strtoupper($initials) }}
</div>
