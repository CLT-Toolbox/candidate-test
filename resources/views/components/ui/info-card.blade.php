@props(['label', 'value', 'icon' => null])

<div class="flex-1 bg-white border border-gray-100/80 rounded-2xl p-4 flex items-start gap-4 transition-all hover:border-brand-200/50 hover:shadow-sm duration-300">
    @if($icon)
        <div class="flex-shrink-0 w-10 h-10 bg-gray-50/50 rounded-xl flex items-center justify-center text-brand-600 ring-1 ring-gray-100/50">
            <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
        </div>
    @endif
    
    <div class="flex flex-col min-w-0">
        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em] mb-1.5 leading-none">
            {{ $label }}
        </span>
        <span class="text-xs font-semibold text-gray-900 truncate tracking-tight leading-relaxed">
            {{ $value }}
        </span>
    </div>
</div>
