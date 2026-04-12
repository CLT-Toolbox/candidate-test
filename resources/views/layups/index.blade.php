<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-500">
                <a href="{{ route('suppliers.index') }}" class="hover:underline">Home</a>
                / <a href="{{ route('suppliers.show', $supplier) }}" class="hover:underline">Suppliers</a>
                / <a href="{{ route('suppliers.layups.show', [$supplier, $layup]) }}" class="hover:underline">Layups</a>
                / {{ $layup->layup_code }}
            </div>
            <div class="flex items-center gap-3">
                <button type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 16 16">
                        <rect x="1" y="1" width="10" height="12" rx="2" stroke="currentColor" stroke-width="1.2"/>
                        <rect x="5" y="4" width="10" height="12" rx="2" stroke="currentColor" stroke-width="1.2" fill="white"/>
                    </svg>
                    Duplicate
                </button>
                <button type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-[#2d5a3d] hover:bg-[#244a32] transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 16 16">
                        <path d="M2 3a1 1 0 011-1h8l3 3v8a1 1 0 01-1 1H3a1 1 0 01-1-1V3z" stroke="white" stroke-width="1.2"/>
                        <rect x="5" y="9" width="6" height="5" rx="0.5" stroke="white" stroke-width="1.2"/>
                        <rect x="5" y="2" width="5" height="3" rx="0.5" stroke="white" stroke-width="1.2"/>
                    </svg>
                    Save Changes
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 px-8 py-6 mb-6">
            <div class="mb-5">
                <div class="flex items-center gap-3 mb-1">
                    <h1 class="text-2xl font-bold text-gray-900">
                        Layup Specification: {{ $layup->layup_code }}
                    </h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Active
                    </span>
                </div>
                <p class="text-sm text-gray-500">{{ $layup->name }}</p>
            </div>
            <div class="grid grid-cols-4 border-t border-gray-200 pt-5">
                <div class="pr-6">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Created By</div>
                    <div class="text-base font-semibold text-gray-900">{{ $layup->created_by ?? 'N/A' }}</div>
                </div>
                <div class="px-6 border-l border-gray-200">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Last Modified</div>
                    <div class="text-base font-semibold text-gray-900">{{ $layup->updated_at->format('M d, Y') }}</div>
                </div>
                <div class="px-6 border-l border-gray-200">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Thickness</div>
                    <div class="text-base font-semibold text-[#3a7d52]">{{ number_format($layup->getTotalThicknessAttribute(), 0) }}mm</div>
                </div>
                <div class="pl-6 border-l border-gray-200">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Layers</div>
                    <div class="text-base font-semibold text-gray-900">{{ $layup->getPlyCountAttribute() }} Layers</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Layer Composition</h2>
                        <button x-data @click="$dispatch('open-modal', 'create-layer')"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-[#3a7d52] hover:bg-gray-50 rounded-lg transition">
                            + Add Layer
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">Order</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Thickness</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Width</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Angle</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Grade</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($layers as $layer)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 text-gray-400 text-base cursor-grab select-none">⋮⋮</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $layer->thickness }}mm</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $layer->width }}mm</td>
                                        <td class="px-6 py-4 text-sm">
                                            @if($layer->angle == 0 || $layer->angle == 180)
                                                <span class="inline-flex items-center gap-1.5 text-[#3a7d52] font-medium">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 12 12">
                                                        <path d="M6 10V2M3 5l3-3 3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    {{ $layer->angle }}°
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 text-amber-700 font-medium">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 12 12">
                                                        <path d="M10 6A4 4 0 1 1 6 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                        <path d="M6 1l1.5 1.5L6 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    {{ $layer->angle }}°
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <span class="inline-flex items-center gap-1.5">
                                                @php $isC24 = str_contains($layer->species_grade ?? '', '24'); @endphp
                                                <span class="w-2 h-2 rounded-full flex-shrink-0 {{ $isC24 ? 'bg-green-600' : 'bg-amber-700' }}"></span>
                                                {{ $layer->species_grade ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="flex items-center gap-3">
                                                <button x-data @click="$dispatch('open-modal', 'edit-layer-{{ $layer->id }}')"
                                                    class="text-[#3a7d52] hover:underline font-medium">Edit</button>
                                                <form method="POST"
                                                    action="{{ route('suppliers.layups.layers.destroy', [$supplier, $layup, $layer]) }}"
                                                    onsubmit="return confirm('Delete this layer?')" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:underline font-medium">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <x-modal name="edit-layer-{{ $layer->id }}">
                                        <form method="POST"
                                            action="{{ route('suppliers.layups.layers.update', [$supplier, $layup, $layer]) }}"
                                            class="p-6">
                                            @csrf @method('PATCH')
                                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Edit Layer</h2>
                                            <div class="grid grid-cols-2 gap-4 mb-4">
                                                <div>
                                                    <x-input-label value="Order" />
                                                    <x-text-input name="layer_order" type="number" class="mt-1 block w-full" value="{{ $layer->layer_order }}" required />
                                                </div>
                                                <div>
                                                    <x-input-label value="Thickness (mm)" />
                                                    <x-text-input name="thickness" type="number" step="0.01" class="mt-1 block w-full" value="{{ $layer->thickness }}" required />
                                                </div>
                                                <div>
                                                    <x-input-label value="Width (mm)" />
                                                    <x-text-input name="width" type="number" step="0.01" class="mt-1 block w-full" value="{{ $layer->width }}" required />
                                                </div>
                                                <div>
                                                    <x-input-label value="Angle (°)" />
                                                    <x-text-input name="angle" type="number" step="0.01" class="mt-1 block w-full" value="{{ $layer->angle }}" required />
                                                </div>
                                                <div class="col-span-2">
                                                    <x-input-label value="Species/Grade" />
                                                    <x-text-input name="species_grade" class="mt-1 block w-full" value="{{ $layer->species_grade }}" />
                                                </div>
                                            </div>
                                            <div class="flex justify-end gap-2">
                                                <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                                <x-primary-button>Save</x-primary-button>
                                            </div>
                                        </form>
                                    </x-modal>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                                            No layers found. Click "+ Add Layer" to get started.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between text-sm text-gray-500">
                        <span>Showing {{ $layers->count() }} layers</span>
                        <span>Calculated Sum: {{ number_format($layup->getTotalThicknessAttribute(), 2) }} mm</span>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-1">Engineering Note</h3>
                            <p class="text-sm text-gray-500 leading-relaxed">
                                Ensure bonding pressure is adjusted for varying layer grades (C24/C16 mix).
                                Verify alignment of 90° transverse layers.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-1">
                <div class="rounded-xl border border-gray-200 overflow-hidden shadow-sm">

                    {{-- Dark header --}}
                    <div class="bg-[#1c1c1e] px-5 py-4">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <h2 class="text-[15px] font-semibold text-white tracking-tight">Structure Visualizer</h2>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-3 h-3 rounded-sm flex-shrink-0" style="background:#dfc28a;"></div>
                                    <span class="text-xs text-gray-300 whitespace-nowrap">Longitudinal (0°)</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <div class="w-3 h-3 rounded-sm flex-shrink-0" style="background:#b07d3a;"></div>
                                    <span class="text-xs text-gray-300 whitespace-nowrap">Transverse (90°)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white px-5 pt-5 pb-6">
                        <div class="flex gap-3">
                            <div class="flex flex-col items-start" style="min-width:54px; padding-top:2px;">
                                <div>
                                    <p class="text-[9px] font-bold uppercase tracking-widest text-gray-700 leading-none">TOP</p>
                                    <p class="text-[9px] font-bold uppercase tracking-widest text-gray-700 leading-none mt-0.5">(OUTSIDE)</p>
                                </div>
                                <div class="flex-1 flex justify-center w-full my-2">
                                    <div class="border-l border-dashed border-gray-400 h-full"></div>
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold uppercase tracking-widest text-gray-700 leading-none">BOTTOM</p>
                                    <p class="text-[9px] font-bold uppercase tracking-widest text-gray-700 leading-none mt-0.5">(INSIDE)</p>
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                {{-- Inner card --}}
                                <div class="rounded-2xl border border-gray-200 bg-gray-50 shadow-sm px-3 py-3 space-y-1.5">
                                    @php
                                        $maxWidth = $layers->max('width') ?: 1200;
                                    @endphp

                                    @forelse($layers as $layer)
                                        @php
                                            $isLong  = ($layer->angle == 0 || $layer->angle == 180);
                                            $wPct    = round(($layer->width / $maxWidth) * 100);
                                            $pxH     = max(intval($layer->thickness * 0.65), 36);
                                            $bg      = $isLong ? '#e8d5a3' : '#b07d3a';
                                            $bdColor = $isLong ? '#cdb87a' : '#8a5f25';
                                            $color   = $isLong ? '#3d2600' : '#fff3e0';
                                        @endphp
                                        <div class="flex justify-center">
                                            <div class="flex items-center justify-between rounded-lg px-3 text-[13px] font-medium"
                                                style="
                                                    width: {{ $wPct }}%;
                                                    height: {{ $pxH }}px;
                                                    background-color: {{ $bg }};
                                                    border: 1px solid {{ $bdColor }};
                                                    color: {{ $color }};
                                                    min-width: 100%;
                                                ">
                                                <span>L{{ $layer->layer_order }} ({{ $layer->thickness }}mm)</span>
                                                @if($isLong)
                                                    {{-- Arrow up —— longitudinal --}}
                                                    <svg class="w-4 h-4 flex-shrink-0 ml-2" fill="none" viewBox="0 0 16 16" style="opacity:0.75;">
                                                        <path d="M8 13V3M4 7l4-4 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                @else
                                                    {{-- Rotate —— transverse --}}
                                                    <svg class="w-4 h-4 flex-shrink-0 ml-2" fill="none" viewBox="0 0 16 16" style="opacity:0.75;">
                                                        <path d="M13 8A5 5 0 1 1 8 3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                        <path d="M8 0.5 L11 3 L8 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-center text-gray-400 text-sm py-10">Add layers to see visualization</p>
                                    @endforelse
                                </div>

                                {{-- Caption --}}
                                <div class="text-center mt-4 px-1">
                                    <p class="text-sm font-medium text-gray-500">Cross-Laminated Structural Assembly</p>
                                    <p class="text-xs italic text-gray-400 mt-1 leading-snug">
                                        Note: 3D orientation is for schematic purposes. All layers bonded with industrial-grade adhesives.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-modal name="create-layer">
            <form method="POST" action="{{ route('suppliers.layups.layers.store', [$supplier, $layup]) }}" class="p-6">
                @csrf
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Add Layer</h2>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-input-label value="Order" />
                        <x-text-input name="layer_order" type="number" class="mt-1 block w-full" placeholder="1" required />
                    </div>
                    <div>
                        <x-input-label value="Thickness (mm)" />
                        <x-text-input name="thickness" type="number" step="0.01" class="mt-1 block w-full" placeholder="40" required />
                    </div>
                    <div>
                        <x-input-label value="Width (mm)" />
                        <x-text-input name="width" type="number" step="0.01" class="mt-1 block w-full" placeholder="1200" required />
                    </div>
                    <div>
                        <x-input-label value="Angle (°)" />
                        <x-text-input name="angle" type="number" step="0.01" class="mt-1 block w-full" placeholder="0" required />
                    </div>
                    <div class="col-span-2">
                        <x-input-label value="Species/Grade" />
                        <x-text-input name="species_grade" class="mt-1 block w-full" placeholder="e.g. C24" />
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                    <x-primary-button>Create</x-primary-button>
                </div>
            </form>
        </x-modal>

    </div>
</x-app-layout>
