@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'error-copy']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
