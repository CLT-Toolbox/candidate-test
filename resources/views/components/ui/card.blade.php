<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-surface-200 shadow-sm overflow-hidden']) }}>
    @if(isset($header))
        <div class="px-6 py-4 border-b border-surface-100 bg-surface-50/50">
            {{ $header }}
        </div>
    @endif

    <div class="px-6 py-5">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-6 py-4 border-t border-surface-100 bg-surface-50/30">
            {{ $footer }}
        </div>
    @endif
</div>
