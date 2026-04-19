@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full px-4 py-2 bg-white border border-surface-200 rounded-lg text-xs text-surface-900 focus:ring-1 focus:ring-brand-500 focus:border-brand-500 placeholder-surface-300 shadow-sm shadow-black/[0.01]']) }}>
