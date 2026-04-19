@props([
    'title',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-row justify-between items-start mb-10 text-left']) }}>
    <div>
        <h2 class="text-xl font-bold text-surface-900 leading-tight" style="font-family: 'Playfair Display', serif;">
            {{ $title }}
        </h2>
        @if($subtitle)
            <p class="mt-1 text-xs text-surface-500 font-medium">
                {{ $subtitle }}
            </p>
        @endif
    </div>

    @if(isset($actions))
        <div class="flex items-center gap-3">
            {{ $actions }}
        </div>
    @endif
</div>
