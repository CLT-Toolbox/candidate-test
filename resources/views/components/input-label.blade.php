@props(['value'])

<label {{ $attributes->merge(['class' => 'text-main block text-sm font-medium']) }}>
    {{ $value ?? $slot }}
</label>
