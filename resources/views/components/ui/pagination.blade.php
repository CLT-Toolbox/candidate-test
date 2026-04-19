@props([
    'paginator',
    'resourceName' => 'Items'
])

<div class="flex items-center justify-between">
    <div class="text-[10px] text-gray-600 uppercase tracking-widest font-bold">
        Showing <span class="text-gray-800">{{ $paginator->firstItem() ?? 0 }}</span> -
        <span class="text-gray-800">{{ $paginator->lastItem() ?? 0 }}</span> of <span
            class="text-gray-800">{{ $paginator->total() }}</span> {{ $resourceName }}
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ $paginator->previousPageUrl() }}"
            class="p-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-white hover:text-brand-600 hover:border-brand-200 transition-all {{ $paginator->onFirstPage() ? 'opacity-30 cursor-not-allowed pointer-events-none' : '' }}">
            <i data-lucide="chevron-left" class="w-4 h-4"></i>
        </a>
        <a href="{{ $paginator->nextPageUrl() }}"
            class="p-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-white hover:text-brand-600 hover:border-brand-200 transition-all {{ !$paginator->hasMorePages() ? 'opacity-30 cursor-not-allowed pointer-events-none' : '' }}">
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </a>
    </div>
</div>
