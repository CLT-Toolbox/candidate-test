<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <x-ui.breadcrumb :items="[
                'Home' => route('dashboard'),
                'Suppliers' => route('suppliers.index'),
                $supplier->name => route('suppliers.show', $supplier->id),
                'Layup Detail' => null
            ]" />
            
            <div class="flex items-center gap-3">
                <form action="{{ route('suppliers.layups.duplicate', [$supplier->id, $layup->id]) }}" method="POST">
                    @csrf
                    <x-ui.button type="submit" variant="secondary" icon="copy" class="rounded-xl border-gray-200">
                        Duplicate
                    </x-ui.button>
                </form>
                <x-ui.button variant="primary" x-cloak icon="save" class="rounded-xl bg-brand-800 shadow-lg shadow-brand-500/20"
                    @click="save()" ::disabled="saving">
                    <span x-show="!saving" class="flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Save Changes
                    </span>
                    <span x-show="saving" class="flex items-center gap-2">
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Saving...
                    </span>
                </x-ui.button>
            </div>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8" 
        x-data="{ 
            layers: @js($layup->layers),
            saving: false,
            initSortable() {
                const tbody = this.$el.querySelector('tbody');
                if (!tbody) return;
                
                Sortable.create(tbody, {
                    animation: 150,
                    handle: '[data-grip]',
                    draggable: 'tr',
                    ghostClass: 'bg-brand-50/50',
                    onEnd: (evt) => {
                        // 1. Move the element back to its original DOM position.
                        // This allows AlpineJS to handle the move correctly via data reactivity.
                        if (evt.from === evt.to) {
                            const parent = evt.from;
                            const children = Array.from(parent.children);
                            if (evt.newIndex > evt.oldIndex) {
                                parent.insertBefore(evt.item, children[evt.oldIndex]);
                            } else {
                                parent.insertBefore(evt.item, children[evt.oldIndex + 1]);
                            }
                        }

                        // 2. Use draggable-aware indices
                        const oldIndex = evt.oldDraggableIndex;
                        const newIndex = evt.newDraggableIndex;
                        
                        if (oldIndex === newIndex || oldIndex === undefined || newIndex === undefined) return;

                        // 3. Update the Alpine array
                        const list = [...this.layers];
                        const [movedItem] = list.splice(oldIndex, 1);
                        list.splice(newIndex, 0, movedItem);
                        
                        this.layers = list;
                        this.refreshIcons();
                        this.notify('Order updated locally. Remember to save changes.');
                    }
                });
            },
            toasts: [],
            notify(message, type = 'success') {
                const id = Date.now();
                this.toasts.push({ id, message, type });
                this.refreshIcons();
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 3000);
            },
            refreshIcons() {
                this.$nextTick(() => {
                    if (window.lucide) {
                        window.lucide.createIcons({ icons: window.lucide.icons });
                    }
                });
            },
            save() {
                this.saving = true;
                window.axios.post('{{ route('suppliers.layups.layers.reorder', [$supplier->id, $layup->id]) }}', {
                    layers: this.layers
                })
                .then(response => {
                    this.notify('Changes saved successfully');
                    setTimeout(() => window.location.reload(), 1000);
                })
                .catch(error => {
                    this.notify('Error saving changes: ' + (error.response?.data?.message || 'Unknown error'), 'error');
                    this.saving = false;
                });
            }
        }"
        x-init="initSortable(); refreshIcons();">
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-12 overflow-hidden relative">
            <div class="flex-1 space-y-3">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
                        Layup Specification: {{ $layup->name }}
                    </h1>
                    <x-ui.badge variant="emerald" class="px-3 py-1 text-[10px]">
                        {{ $layup->status }}
                    </x-ui.badge>
                </div>
                <p class="text-sm text-gray-400 font-medium max-w-lg leading-relaxed">
                    Standard <span x-text="layers.length"></span>-layer panel for residential structural walls. High-fidelity specification for industrial grade cross-laminated timber.
                    <style>
                        [x-cloak] { display: none !important; }
                    </style>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-12 border-l border-gray-50 pl-12 h-full">
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">Created By</p>
                    <p class="text-sm font-bold text-gray-800">{{ $layup->created_by }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">Last Modified</p>
                    <p class="text-sm font-bold text-gray-800">{{ $layup->updated_at->format('M d, Y') }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Thickness</p>
                    <p class="text-3xl font-bold text-emerald-600" x-text="layers.reduce((sum, l) => sum + parseFloat(l.thickness), 0) + 'mm'"></p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Layers</p>
                    <p class="text-3xl font-bold text-emerald-600" x-text="layers.length + ' Layers'"></p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">
            <div class="xl:col-span-7 space-y-6">
                <div class="flex items-center justify-between px-2">
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Layer Composition</h3>
                    <x-ui.button variant="ghost" icon="plus" class="text-brand-700 hover:bg-brand-50 font-bold text-xs" 
                        @click="$dispatch('open-modal', 'add-layer')">
                        Add Layer
                    </x-ui.button>
                </div>

                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                    <x-ui.table>
                        <x-slot name="thead">
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-left w-12 text-center">Order</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-left">Thickness</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-left">Width</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Angle</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-left">Grade</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Actions</th>
                        </x-slot>

                        <template x-for="(layer, index) in layers" :key="layer.id">
                            <tr class="group hover:bg-gray-50/50 transition-all border-b border-gray-50 last:border-0">
                                <td class="px-6 py-4 text-center">
                                    <div data-grip class="w-8 h-8 mx-auto rounded-lg bg-gray-50 flex items-center justify-center text-gray-300 group-hover:text-gray-900 group-hover:bg-white border border-transparent group-hover:border-gray-100 transition-all cursor-grab active:cursor-grabbing shadow-xs">
                                        <i data-lucide="grip-vertical" class="w-4 h-4"></i>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs font-bold text-gray-900"><span x-text="Math.round(layer.thickness)"></span>mm</td>
                                <td class="px-6 py-4 text-xs font-semibold text-gray-500"><span x-text="Math.round(layer.width)"></span>mm</td>
                                <td class="px-6 py-4 text-center">
                                    <template x-if="layer.angle == 0">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-50 text-gray-600 rounded-full border border-gray-100 text-[10px] font-bold">
                                            <i data-lucide="arrow-up" class="w-3 h-3"></i> 0°
                                        </span>
                                    </template>
                                    <template x-if="layer.angle != 0">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-50 text-orange-600 rounded-full border border-orange-100 text-[10px] font-bold">
                                            <i data-lucide="rotate-cw" class="w-3 h-3"></i> <span x-text="layer.angle"></span>°
                                        </span>
                                    </template>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full" :class="layer.grade === 'C24' ? 'bg-emerald-500' : 'bg-orange-500'"></span>
                                        <span class="text-xs font-bold text-gray-700" x-text="layer.grade"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1 opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity">
                                        <x-ui.button variant="ghost" icon="edit-3" size="icon" class="text-yellow-400 hover:text-yellow-600 hover:bg-yellow-50" 
                                            @click="$dispatch('open-modal', 'edit-layer'); $dispatch('edit-layer', { id: layer.id, layer_order: layer.layer_order, thickness: layer.thickness, width: layer.width, angle: layer.angle, grade: layer.grade })" />
                                        <x-ui.button variant="ghost" icon="trash-2" size="icon" class="text-red-400 hover:text-red-600 hover:bg-red-50" 
                                            @click="$dispatch('open-modal', 'delete-layer'); $dispatch('delete-layer', { id: layer.id })" />
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </x-ui.table>
                    <div class="px-6 py-4 bg-gray-50/30 flex items-center justify-between border-t border-gray-50">
                        <span class="text-[10px] font-bold text-gray-400 uppercase" x-text="'Showing ' + layers.length + ' layers'"></span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Calculated Sum: <span class="text-gray-900" x-text="layers.reduce((sum, l) => sum + parseFloat(l.thickness), 0).toFixed(2) + ' mm'"></span></span>
                    </div>
                </div>

                <div class="p-6 bg-white rounded-3xl border border-gray-100 shadow-sm flex items-start gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 shrink-0 shadow-sm border border-brand-100">
                        <i data-lucide="info" class="w-5 h-5"></i>
                    </div>
                    <div class="space-y-2">
                        <h4 class="text-sm font-bold text-gray-900">Engineering Note</h4>
                        <p class="text-xs text-gray-400 font-medium leading-relaxed">
                            Ensure bonding pressure is adjusted for varying layer grades (C24/C16 mix). Verify alignment of 90° transverse layers according to structural load bearing requirements for the intended installation environment.
                        </p>
                    </div>
                </div>
            </div>

            <div class="xl:col-span-5 space-y-6">
                <div class="flex items-center justify-between px-2">
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Structure Visualizer</h3>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 bg-[#E2C4A2] rounded-sm"></span>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Longitudinal (0°)</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 bg-[#C69C6D] rounded-sm"></span>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Transverse (90°)</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-12 min-h-[600px] flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-y-12 left-10 border-l border-dashed border-gray-200 flex flex-col justify-between">
                        <div class="relative pl-3">
                            <span class="absolute left-0 top-0 w-2 border-t border-gray-200"></span>
                            <p class="text-[8px] font-bold text-gray-300 uppercase leading-none tracking-widest -mt-1">Top<br>(Outside)</p>
                        </div>
                        <div class="relative pl-3">
                            <span class="absolute left-0 bottom-0 w-2 border-b border-gray-200"></span>
                            <p class="text-[8px] font-bold text-gray-300 uppercase leading-none tracking-widest -mb-1">Bottom<br>(Inside)</p>
                        </div>
                    </div>

                    <div class="space-y-1 relative z-10 w-full max-w-sm flex flex-col shadow-2xl rounded-2xl overflow-hidden shadow-brand-900/10">
                        <template x-for="(layer, index) in layers" :key="layer.id">
                            <div class="relative group cursor-pointer transition-all duration-300 hover:scale-[1.02] border-y border-white/20 first:border-b-0 last:border-t-0"
                                 :style="`height: ${layer.thickness * 2.5}px; background-color: ${layer.angle == 0 ? '#E2C4A2' : '#C69C6D'}`"
                                 :class="layer.angle != 0 ? 'px-4' : 'px-2'">
                                <div class="w-full h-full flex items-center justify-between px-4">
                                    <div class="flex items-center gap-4">
                                        <span class="text-[10px] font-bold text-brand-950/40" x-text="'L' + (index + 1) + ' (' + Math.round(layer.thickness) + 'mm)'"></span>
                                    </div>
                                    <i :data-lucide="layer.angle == 0 ? 'arrow-up' : 'rotate-cw'" class="w-3.5 h-3.5 text-brand-950/30"></i>
                                </div>
                                <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                        </template>
                    </div>

                    <div class="absolute bottom-8 left-0 right-0 text-center">
                        <p class="text-xs font-bold text-gray-400 capitalize">Cross-Laminated Structural Assembly</p>
                        <p class="text-[9px] text-gray-300 italic">Note: 3D orientation is for schematic purposes. All layers bonded with industrial-grade adhesives.</p>
                    </div>
                </div>
            </div>
        </div>
        @include('suppliers.show.show-detail.partials.modals')
    </div>

    {{-- Global Toast Notifications --}}
    <div class="fixed bottom-8 right-8 z-[100] space-y-3 pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-4"
                 class="pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-2xl shadow-2xl border min-w-[300px]"
                 :class="{
                     'bg-white border-brand-100 text-brand-900': toast.type === 'success',
                     'bg-red-50 border-red-100 text-red-900': toast.type === 'error'
                 }">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0"
                     :class="toast.type === 'success' ? 'bg-brand-50 text-brand-600' : 'bg-red-100 text-red-600'">
                    <i :data-lucide="toast.type === 'success' ? 'check-circle' : 'alert-circle'" class="w-4 h-4"></i>
                </div>
                <p class="text-xs font-bold" x-text="toast.message"></p>
            </div>
        </template>
    </div>
</x-app-layout>
