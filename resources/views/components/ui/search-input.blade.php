@props([
    'placeholder' => 'Search...',
    'name' => 'search',
    'value' => '',
])

<div {{ $attributes->merge(['class' => 'relative group']) }}>
    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-surface-400">
        <i data-lucide="search" class="w-4 h-4"></i>
    </div>
    <input 
        type="text" 
        name="{{ $name }}" 
        value="{{ $value }}"
        placeholder="{{ $placeholder }}" 
        class="block w-full pl-10 pr-4 py-2 bg-white border border-surface-200 rounded-lg text-xs text-surface-900 focus:ring-1 focus:ring-brand-500 focus:border-brand-500 placeholder-surface-300 shadow-sm shadow-black/[0.01]"
    >
</div>
