<div class="bg-white rounded-lg border border-gray-200 shadow-[0_4px_20px_rgb(0,0,0,0.03)] px-8 py-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex flex-col gap-2">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">
                    {{ $supplier->name }}
                </h1>
                <x-ui.badge variant="success" class="text-[10px] px-2.5 py-0.5 bg-emerald-700 text-white border-emerald-100 transition-none">
                    Active Partner
                </x-ui.badge>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">
                    ID: SUP-{{ str_pad($supplier->id, 4, '0', STR_PAD_LEFT) }}-{{ now()->year }}
                </span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <x-ui.button variant="secondary" icon="edit-3" class="px-6 rounded-xl hover:border-brand-300 transition-all duration-300 group">
                <span class="relative">Edit Supplier</span>
            </x-ui.button>
        </div>
    </div>
</div>
