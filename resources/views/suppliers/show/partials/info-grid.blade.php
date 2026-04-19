<div class="bg-white rounded-lg border border-gray-200 shadow-[0_2px_12px_rgb(0,0,0,0.02)] overflow-hidden">
    <div class="grid grid-cols-1 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-100">
        <div class="px-8 py-5 flex items-start gap-4">
            <div class="mt-1 text-brand-600">
                <i data-lucide="mail" class="w-4 h-4"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1.5">Primary Contact</span>
                <span class="text-xs font-semibold text-gray-900 tracking-tight">{{ 'engineering@' . Str::slug($supplier->name) . '.ca' }}</span>
            </div>
        </div>

        <div class="px-8 py-5 flex items-start gap-4">
            <div class="mt-1 text-brand-600">
                <i data-lucide="map-pin" class="w-4 h-4"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1.5">Location</span>
                <span class="text-xs font-semibold text-gray-900 tracking-tight">Montreal, QC, Canada</span>
            </div>
        </div>

        <div class="px-8 py-5 flex items-start gap-4">
            <div class="mt-1 text-brand-600">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1.5">Material Certifications</span>
                <span class="text-xs font-semibold text-gray-900 tracking-tight">SPF No. 1/2, D. Fir-L</span>
            </div>
        </div>

        <div class="px-8 py-5 flex items-start gap-4">
            <div class="mt-1 text-brand-600">
                <i data-lucide="calendar" class="w-4 h-4"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1.5">Last Audit Date</span>
                <span class="text-xs font-semibold text-gray-900 tracking-tight">{{ now()->subMonths(6)->format('M d, Y') }}</span>
            </div>
        </div>
    </div>
</div>
