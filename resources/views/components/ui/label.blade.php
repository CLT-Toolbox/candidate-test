@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xxs font-bold text-surface-400 uppercase tracking-widest mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>
