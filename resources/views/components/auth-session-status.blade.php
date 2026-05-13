@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'success-copy']) }}>
        {{ $status }}
    </div>
@endif
