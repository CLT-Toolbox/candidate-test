<div class="space-y-6 flex flex-col h-[500px]">
    <div class="flex items-center justify-between pb-2 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <button @click="step = 'upload'" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-50 hover:text-gray-900 transition-all border border-transparent hover:border-gray-100">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </button>
            <div>
                <h4 class="text-xs font-bold text-gray-900">Conflict Details</h4>
                <p class="text-[10px] text-gray-500 font-medium uppercase tracking-tight">Review differences per item</p>
            </div>
        </div>
        <div class="flex p-1 bg-gray-100 rounded-lg">
            <button @click="applyToAll('skip')" :class="strategy === 'skip' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500'" class="px-3 py-1 text-[9px] font-bold rounded-md transition-all">KEEP ALL</button>
            <button @click="applyToAll('overwrite')" :class="strategy === 'overwrite' ? 'bg-white shadow-sm text-brand-700' : 'text-gray-500'" class="px-3 py-1 text-[9px] font-bold rounded-md transition-all">USE INCOMING</button>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar space-y-4">
        <template x-for="(conflict, index) in (analysis?.conflicts || [])" :key="conflict.name">
            <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white shadow-sm hover:border-gray-300 transition-colors">
                <div class="px-4 py-3 bg-gray-50 flex items-center justify-between border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 bg-white rounded-lg flex items-center justify-center border border-gray-200 text-amber-500 shadow-sm">
                            <i data-lucide="git-merge" class="w-3.5 h-3.5"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-900" x-text="conflict.name"></span>
                    </div>
                    <div class="flex items-center gap-1.5 p-1 bg-gray-200/50 rounded-lg">
                        <button @click="resolutions[conflict.name] = 'skip'" :class="resolutions[conflict.name] === 'skip' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500'" class="px-3 py-1 text-[9px] font-bold rounded-md transition-all uppercase">Existing</button>
                        <button @click="resolutions[conflict.name] = 'overwrite'" :class="resolutions[conflict.name] === 'overwrite' ? 'bg-white shadow-sm text-brand-700' : 'text-gray-500'" class="px-3 py-1 text-[9px] font-bold rounded-md transition-all uppercase">Incoming</button>
                    </div>
                </div>

                <div class="p-5 grid grid-cols-2 gap-10 relative">
                    <div class="absolute left-1/2 top-5 bottom-5 w-px bg-gray-100 -translate-x-1/2"></div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center gap-1.5 mb-2 opacity-60">
                            <i data-lucide="database" class="w-3 h-3 text-gray-400"></i>
                            <span class="text-[9px] font-bold uppercase tracking-widest text-gray-400">Database</span>
                        </div>
                        <template x-for="layer in conflict.layer_conflicts" :key="layer.layer_order">
                            <div class="p-3 bg-gray-50 border border-gray-100 rounded-xl" :class="resolutions[conflict.name] === 'skip' ? 'ring-1 ring-gray-300 bg-white' : 'opacity-40'">
                                <span class="text-[9px] font-bold text-gray-300 uppercase block mb-1.5 px-0.5">Layer <span x-text="layer.layer_order"></span></span>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-[8px] font-semibold text-gray-400 uppercase tracking-tighter">THICKNESS</p>
                                        <p class="text-xs font-medium text-gray-700" x-text="layer.existing.thickness + 'mm'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[8px] font-semibold text-gray-400 uppercase tracking-tighter">WIDTH</p>
                                        <p class="text-xs font-medium text-gray-700" x-text="layer.existing.width + 'mm'"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center gap-1.5 mb-2">
                            <i data-lucide="file-json" class="w-3 h-3 text-brand-500"></i>
                            <span class="text-[9px] font-bold uppercase tracking-widest text-brand-600">Incoming</span>
                        </div>
                        <template x-for="layer in conflict.layer_conflicts" :key="layer.layer_order">
                            <div class="p-3 border transition-all rounded-xl" 
                                :class="resolutions[conflict.name] === 'overwrite' ? 'bg-brand-50/30 border-brand-200 ring-1 ring-brand-100' : 'bg-white border-gray-100 opacity-40'">
                                <div class="flex items-center justify-between mb-1.5 px-0.5">
                                    <span class="text-[9px] font-bold text-brand-300 uppercase block">Layer <span x-text="layer.layer_order"></span></span>
                                    <template x-if="layer.existing.thickness != layer.incoming.thickness || layer.existing.width != layer.incoming.width">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full shadow-sm shadow-red-200 animate-pulse"></span>
                                    </template>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-[8px] font-semibold text-gray-400 uppercase tracking-tighter">THICKNESS</p>
                                        <p class="text-xs font-medium" :class="layer.existing.thickness != layer.incoming.thickness ? 'text-red-600 font-bold' : 'text-gray-700'" x-text="layer.incoming.thickness + 'mm'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[8px] font-semibold text-gray-400 uppercase tracking-tighter">WIDTH</p>
                                        <p class="text-xs font-medium" :class="layer.existing.width != layer.incoming.width ? 'text-red-600 font-bold' : 'text-gray-700'" x-text="layer.incoming.width + 'mm'"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
        <div class="flex items-center gap-2 text-gray-400">
            <i data-lucide="info" class="w-3.5 h-3.5"></i>
            <span class="text-[10px] font-medium italic">Changes are not applied until final confirmation</span>
        </div>
        <x-ui.button variant="primary" size="sm" @click="step = 'upload'">
            Back to Summary
        </x-ui.button>
    </div>
</div>
