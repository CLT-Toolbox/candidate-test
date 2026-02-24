<x-app-layout>
<div class="min-h-screen bg-gray-50 font-sans">
    <main class="max-w-7xl mx-auto px-6 py-6">
        @if(session('success'))
            <div id="successAlert" class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div id="errorAlert" class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-1.5 text-sm text-gray-500 mb-6">
            <a href="/" class="hover:text-gray-700">Home</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            <a href="{{ route('suppliers') }}" class="hover:text-gray-700">Suppliers</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            <a href="{{ route('suppliers.show', $layup->supplier_id) }}" class="hover:text-gray-700">Layups</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            <span class="text-gray-900 font-medium">{{ $layup->name }}</span>
        </nav>

        <!-- Header Section -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-bold text-gray-900">Layup Specification: {{ $layup->name }}</h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($layup->status == 1) bg-green-100 text-green-800 border border-green-200
                            @elseif($layup->status == 0) bg-yellow-100 text-yellow-800 border border-yellow-200
                            @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                            @if($layup->status == 1) Active @elseif($layup->status == 0) Draft @else Archived @endif
                        </span>
                    </div>
                    <p class="text-gray-600 mt-2">{{ $layup->species_grade ?? 'Standard layup specification' }}</p>
                </div>
                <div class="flex gap-3">
                    <button class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-2a2 2 0 00-2-2h-8a2 2 0 00-2 2v2a2 2 0 002 2z"/></svg>
                        Duplicate
                    </button>
                    <button onclick="saveAllChanges()" id="saveBtn" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-green-900 text-white hover:bg-green-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        Save Changes
                    </button>
                </div>
            </div>

            <!-- Meta Info Grid -->
            <div class="grid grid-cols-4 divide-x divide-gray-100 mt-6 pt-6 border-t border-gray-100">
                <div class="pr-6">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Created By</p>
                    <p class="text-sm text-gray-900 font-medium">Eng. Dept A</p>
                </div>
                <div class="px-6">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Last Modified</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $layup->updated_at->format('M d, Y') }}</p>
                </div>
                <div class="px-6">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Thickness</p>
                    <p id="totalThicknessDisplay" class="text-sm text-gray-900 font-medium">
                        {{ $layup->layers ? $layup->layers->sum('thickness') . 'mm' : '0mm' }}
                    </p>
                </div>
                <div class="pl-6">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Layers</p>
                    <p id="totalLayersDisplay" class="text-sm text-gray-900 font-medium">{{ $layup->layers ? $layup->layers->count() : 0 }} Layers</p>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-">
            <!-- Layer Composition Section -->
            <div class="col-span-2">
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <!-- Toolbar -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <h2 class="font-semibold text-gray-900">Layer Composition</h2>
                        <button onclick="openAddLayerModal()" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-sm font-medium bg-green-900 text-white hover:bg-green-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Add Layer
                        </button>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50">
                                <th class="text-left px-6 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Order</th>
                                <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Thickness</th>
                                <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Width</th>
                                <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Angle</th>
                                <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Grade</th>
                                <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50" id="layersTableBody" data-layup-id="{{ $layup->id }}">
                            @forelse ($layup->layers ?? [] as $layer)
                                <tr class="hover:bg-gray-50 transition-colors cursor-move" data-layer-id="{{ $layer->id }}" data-original-id="{{ $layer->id }}" data-thickness="{{ $layer->thickness }}" data-width="{{ $layer->width }}" data-angle="{{ $layer->angle }}">
                                    <td class="px-6 py-3.5">
                                        <div class="flex items-center gap-2 text-gray-700">
                                            <svg class="w-4 h-4 text-gray-400 cursor-grab handle" fill="currentColor" viewBox="0 0 20 20"><path d="M8 9a2 2 0 100-4 2 2 0 000 4zm0 2a2 2 0 100-4 2 2 0 000 4zm0 2a2 2 0 100-4 2 2 0 000 4zM12 9a2 2 0 100-4 2 2 0 000 4zm0 2a2 2 0 100-4 2 2 0 000 4zm0 2a2 2 0 100-4 2 2 0 000 4z"/></svg>
                                            <span class="order-display">{{ $layer->layer_order }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="thickness-display text-gray-800">
                                            {{ rtrim(rtrim(number_format($layer->thickness, 2, '.', ','), '0'), '.') }}mm
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="width-display text-gray-800">
                                            {{ rtrim(rtrim(number_format($layer->width, 2, '.', ','), '0'), '.') }}mm
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="angle-display text-gray-800">
                                            {{ rtrim(rtrim(number_format($layer->angle, 2, '.', ','), '0'), '.') }}&deg;
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                            <span class="w-2 h-2 rounded-full bg-green-500"></span>C24
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="relative">
                                            <button class="px-2 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded-full" onclick="toggleRowMenu(event, '{{ $layer->id }}')" aria-expanded="false">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zm0 7a2 2 0 110-4 2 2 0 010 4zm0 7a2 2 0 110-4 2 2 0 010 4z"/></svg>
                                            </button>                                            
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="noLayersRow">
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">No layers added yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 text-xs text-gray-500">
                        Showing <span id="showingCount">{{ $layup->layers ? $layup->layers->count() : 0 }}</span> layers
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Add Layer Modal -->
<div id="addLayerModalBackdrop" class="fixed inset-0 bg-black bg-opacity-40 z-40 transition-opacity hidden" onclick="closeAddLayerModal()"></div>

<div id="addLayerModalContainer" class="fixed inset-0 flex items-center justify-center z-50 p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full border border-gray-200" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100">
            <h3 id="modalTitle" class="text-base font-semibold text-gray-900">Add Layer</h3>
            <button onclick="closeAddLayerModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-6">
            <form id="addLayerForm" onsubmit="addNewLayerLocal(event)">
                <input type="hidden" name="layup_id" value="{{ $layup->id }}">
                <input type="hidden" id="editingLayerId" name="editing_id" value="">

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-1.5">Layer Order</label>
                    <input type="number" name="layer_order" placeholder="e.g., 1 (leave empty to append)" min="1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-700">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-1.5">Thickness (mm)</label>
                    <input type="number" name="thickness" placeholder="e.g., 40" step="0.01" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-700">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-1.5">Width (mm)</label>
                    <input type="number" name="width" placeholder="e.g., 1200" step="0.01" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-700">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-medium mb-1.5">Angle (degrees)</label>
                    <input type="number" name="angle" placeholder="e.g., 0 or 90" step="1" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-700">
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeAddLayerModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="addLayerSubmitBtn"
                        class="flex-1 px-4 py-2 bg-green-900 hover:bg-green-800 text-white rounded-lg text-sm font-medium transition-colors">
                        Add Layer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirm Delete handled by SweetAlert2 (see script include below) -->

<form id="batchSaveForm" method="POST" action="{{ route('clt-layups.batch-update', $layup->id) }}" style="display:none">
    @csrf
    <input type="hidden" id="changesJson" name="changes" value="{}">
</form>

<!-- Shared Action Menu (single instance) -->
<div id="sharedRowMenu" class="absolute z-50 hidden bg-white border border-gray-200 rounded shadow w-40" style="min-width:160px;">
    <div onclick="event.stopPropagation()">
        <button id="sharedEditBtn" class="w-full text-left px-3 py-2 text-sm text-yellow-700 hover:bg-gray-50">Edit</button>
        <button id="sharedDeleteBtn" class="w-full text-left px-3 py-2 text-sm text-red-700 hover:bg-gray-50">Delete</button>
    </div>
</div>

@push('scripts')
    <script src="{{asset('/js/layer.js')}}"></script>
@endpush
</x-app-layout>
