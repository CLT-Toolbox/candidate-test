@php
$initialLayers = $layup->layers
->map(function ($l) {
$angle = (float) $l->angle;
$defaultGrade = abs($angle - 90) < 0.01 ? 'C16' : 'C24' ; return [ 'thickness'=> (float) $l->thickness,
    'width' => (float) $l->width,
    'angle' => $angle,
    'grade' => $l->grade ?? $defaultGrade,
    ];
    })
    ->values()
    ->all();

    if (count($initialLayers) === 0) {
    $initialLayers = [
    ['thickness' => 40, 'width' => 1200, 'angle' => 0, 'grade' => 'C24'],
    ['thickness' => 20, 'width' => 1200, 'angle' => 90, 'grade' => 'C16'],
    ['thickness' => 40, 'width' => 1200, 'angle' => 0, 'grade' => 'C24'],
    ['thickness' => 20, 'width' => 1200, 'angle' => 90, 'grade' => 'C16'],
    ['thickness' => 40, 'width' => 1200, 'angle' => 0, 'grade' => 'C24'],
    ];
    }
    @endphp
    <x-app-layout>
        <div class="border-t border-gray-200/80 bg-gray-100 pb-12 pt-2 text-slate-800 antialiased" x-data="layupSpecificationEditor()">

            @if (session('success'))
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-4 rounded-lg border border-emerald-200/80 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 shadow-sm">
                    {{ session('success') }}
                </div>
            </div>
            @endif

            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                {{-- Toolbar --}}
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-[13px] text-slate-500">
                        <a href="{{ route('dashboard') }}" class="transition hover:text-slate-800">Home</a>
                        <span class="text-slate-300 select-none">&gt;</span>
                        <a href="{{ route('suppliers.index') }}" class="transition hover:text-slate-800">Suppliers</a>
                        <span class="text-slate-300 select-none">&gt;</span>
                        <a href="{{ route('suppliers.layups.index', $supplier->supplier_id) }}" class="transition hover:text-slate-800">Layups</a>
                        <span class="text-slate-300 select-none">&gt;</span>
                        <span class="font-medium text-slate-700">{{ $layup->layup_id }}</span>
                    </nav>
                    <div class="flex items-center gap-2.5">
                        <form method="POST" action="{{ route('suppliers.layups.duplicate', [$supplier->supplier_id, $layup->layup_id]) }}">
                            @csrf
                            <button type="submit" class="inline-flex h-10 items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400/30">
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Duplicate
                            </button>
                        </form>
                        <button type="button" @click="saveLayers()" class="inline-flex h-10 items-center gap-2 rounded-lg bg-[#2D5A47] px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-[#264d3d] focus:outline-none focus:ring-2 focus:ring-[#2D5A47]/40 disabled:cursor-not-allowed disabled:opacity-60" :disabled="saving">
                            <svg class="h-4 w-4 shrink-0 opacity-95" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V8l-5-5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 3v4h4M10 13h4M10 17h4" />
                            </svg>
                            <span x-text="saving ? 'Saving…' : 'Save Changes'"></span>
                        </button>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="mb-10 overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">
                    <div class="grid gap-8 p-6 sm:p-8 lg:grid-cols-[minmax(0,1.2fr)_auto] lg:items-start lg:gap-12">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                                <h1 class="text-[1.375rem] font-bold leading-snug tracking-tight text-slate-900 sm:text-2xl">
                                    Layup Specification: {{ $layup->layup_id }}
                                </h1>
                                @if ($layup->status === 'ACTIVE')
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-[#1f5c45] ring-1 ring-emerald-200/80">Active</span>
                                @elseif($layup->status === 'DRAFT')
                                <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-900 ring-1 ring-amber-200/80">Draft</span>
                                @elseif($layup->status === 'ARCHIVED')
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">Archived</span>
                                @endif
                            </div>
                            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-600">
                                {{ $layup->description ?: '—' }}</p>
                        </div>

                        <div class="flex min-w-0 flex-col gap-8 border-t border-slate-100 pt-8 sm:flex-row sm:flex-wrap sm:items-start sm:gap-0 sm:border-t-0 sm:pt-0 lg:flex-nowrap lg:divide-x lg:divide-slate-200">
                            <div class="flex gap-10 sm:gap-12 lg:pr-10">
                                <div>
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">Created By</p>
                                    <p class="mt-1.5 text-sm font-semibold text-slate-900">{{ $supplier->name }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">Last Modified</p>
                                    <p class="mt-1.5 text-sm font-semibold text-slate-900">{{ $layup->updated_at->format('M j, Y') }}</p>
                                </div>
                            </div>
                            <div class="sm:pl-10 lg:pl-10">
                                <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">Total Thickness</p>
                                <p class="mt-1.5 text-2xl font-bold tabular-nums tracking-tight text-[#2D5A47] sm:text-[1.75rem]" x-text="formatMm(totalThickness())"></p>
                            </div>
                            <div class="sm:pl-10 lg:pl-10">
                                <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">Total Layers</p>
                                <p class="mt-1.5 text-2xl font-bold leading-none text-[#2D5A47] sm:text-[1.75rem]">
                                    <span x-text="layers.length"></span><span class="text-base font-semibold text-[#2D5A47]/85"> layers</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-8">
                    {{-- Layers --}}
                    <div class="space-y-4 lg:col-span-7">
                        <div class="flex items-end justify-between gap-4 border-b border-slate-200/80 pb-3">
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Layer Composition</h2>
                                <p class="mt-0.5 text-xs text-slate-500">Drag rows to reorder. Values update the visualizer.</p>
                            </div>
                            <button type="button" @click="addLayer()" class="inline-flex shrink-0 items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-semibold text-[#2D5A47] transition hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-[#2D5A47]/25">
                                <span class="text-base leading-none">+</span> Add Layer
                            </button>
                        </div>

                        <div class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">
                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[640px] text-sm">
                                    <thead>
                                        <tr class="border-b border-slate-200 bg-slate-50/90">
                                            <th class="w-12 px-3 py-3.5"></th>
                                            <th class="px-4 py-3.5 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">Order</th>
                                            <th class="px-4 py-3.5 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">Thickness</th>
                                            <th class="px-4 py-3.5 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">Width</th>
                                            <th class="px-4 py-3.5 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">Angle</th>
                                            <th class="px-4 py-3.5 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">Grade</th>
                                            <th class="px-4 py-3.5 text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <template x-for="(layer, index) in layers" :key="index">
                                            <tr class="group bg-white transition-colors hover:bg-slate-50/80" draggable="true" @dragstart="dragStart($event, index)" @dragend="dragEnd()" @dragover.prevent="dragOver($event, index)" @drop.prevent="drop(index)">
                                                <td class="px-3 py-3 text-slate-400 cursor-grab active:cursor-grabbing" title="Drag to reorder">
                                                    <svg class="mx-auto h-5 w-5 opacity-70 group-hover:opacity-100" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <circle cx="9" cy="7" r="1.35" />
                                                        <circle cx="15" cy="7" r="1.35" />
                                                        <circle cx="9" cy="12" r="1.35" />
                                                        <circle cx="15" cy="12" r="1.35" />
                                                        <circle cx="9" cy="17" r="1.35" />
                                                        <circle cx="15" cy="17" r="1.35" />
                                                    </svg>
                                                </td>
                                                <td class="px-4 py-3 font-medium tabular-nums text-slate-700" x-text="index + 1"></td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-1.5">
                                                        <input type="number" step="0.01" min="0" x-model.number="layer.thickness" @input="normalizeLayer(layer)" class="h-9 w-[5.5rem] rounded-md border border-slate-200 bg-white px-2.5 text-sm text-slate-900 shadow-sm transition focus:border-[#2D5A47] focus:outline-none focus:ring-2 focus:ring-[#2D5A47]/20" />
                                                        <span class="text-xs text-slate-400">mm</span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-1.5">
                                                        <input type="number" step="0.01" min="0" x-model.number="layer.width" @input="normalizeLayer(layer)" class="h-9 w-[6.25rem] rounded-md border border-slate-200 bg-white px-2.5 text-sm text-slate-900 shadow-sm transition focus:border-[#2D5A47] focus:outline-none focus:ring-2 focus:ring-[#2D5A47]/20" />
                                                        <span class="text-xs text-slate-400">mm</span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-2">
                                                        <input type="number" step="1" min="0" max="360" x-model.number="layer.angle" @input="normalizeAngle(layer)" class="h-9 w-14 rounded-md border border-slate-200 bg-white px-2 text-sm shadow-sm transition focus:border-[#2D5A47] focus:outline-none focus:ring-2 focus:ring-[#2D5A47]/20" :class="isTransverse(layer.angle) ? 'font-medium text-amber-800' : 'font-medium text-[#2D5A47]'" />
                                                        <span class="text-xs text-slate-400">°</span>
                                                        <span x-show="!isTransverse(layer.angle)" class="text-[#2D5A47]" title="Longitudinal">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                                                        </span>
                                                        <span x-show="isTransverse(layer.angle)" class="text-amber-700" title="Transverse">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5c.333.667 1.333 2 3 2s2.667-1.333 3-2V4M14 4v5c.333.667 1.333 2 3 2s2.667-1.333 3-2V4M8 20h8" /></svg>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-2">
                                                        <span class="h-2.5 w-2.5 shrink-0 rounded-full shadow-sm" :class="gradeDotClass(layer.grade)"></span>
                                                        <input type="text" x-model="layer.grade" maxlength="16" class="h-9 w-[4.5rem] rounded-md border border-slate-200 bg-white px-2 text-sm uppercase tracking-wide text-slate-800 shadow-sm transition focus:border-[#2D5A47] focus:outline-none focus:ring-2 focus:ring-[#2D5A47]/20" />
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-right">
                                                    <button type="button" @click="removeLayer(index)" class="text-sm font-medium text-rose-600 transition hover:text-rose-800">Remove</button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex flex-col gap-1 border-t border-slate-200 bg-slate-50/90 px-4 py-3.5 text-sm sm:flex-row sm:items-center sm:justify-between">
                                <span class="text-slate-600">Showing <span class="font-medium text-slate-800" x-text="layers.length"></span> layers</span>
                                <span class="text-slate-600">Calculated sum: <span class="font-semibold tabular-nums text-[#2D5A47]" x-text="totalThickness().toFixed(2) + ' mm'"></span></span>
                            </div>
                        </div>

                        <div class="flex gap-4 rounded-xl border border-slate-200/90 bg-slate-50/60 p-4 shadow-sm sm:p-5">
                            <div class="mt-0.5 shrink-0 text-[#2D5A47]">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Engineering note</p>
                                <p class="mt-1 text-sm leading-relaxed text-slate-600">
                                    Maintain specified bonding pressure and grain alignment during lamination. Verify layer order against structural drawings before production.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Visualizer --}}
                    <div class="lg:col-span-5">
                        <div class="border-b border-slate-200/80 pb-3">
                            <h2 class="text-base font-bold text-slate-900">Structure Visualizer</h2>
                            <div class="mt-3 flex flex-wrap gap-x-6 gap-y-2 text-xs text-slate-600">
                                <span class="inline-flex items-center gap-2">
                                    <span class="h-3 w-3 rounded border border-amber-900/15 bg-[#e8d4b8] shadow-sm"></span>
                                    Longitudinal (0°)
                                </span>
                                <span class="inline-flex items-center gap-2">
                                    <span class="h-3 w-3 rounded border border-amber-900/25 bg-[#c68642] shadow-sm"></span>
                                    Transverse (90°)
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 overflow-hidden rounded-xl border border-slate-200/90 bg-white p-5 shadow-sm sm:p-6">
                            <p class="text-center text-[10px] font-semibold uppercase tracking-widest text-slate-400">Top (outside)</p>
                            <div class="mx-auto mt-2 max-w-[200px] rounded-lg border border-slate-200/80 bg-gradient-to-b from-slate-100 to-slate-50 p-3 shadow-inner">
                                <div class="flex h-[280px] max-h-[340px] flex-col gap-px overflow-hidden rounded-md bg-slate-200/80 p-px sm:h-[300px]">
                                    <template x-for="(layer, index) in layers" :key="'v-' + index">
                                        <div class="flex min-h-[26px] flex-col items-center justify-center gap-0.5 rounded-sm border border-black/[0.06] px-1 py-1.5 text-center text-[11px] font-semibold text-slate-900 shadow-sm" :class="isTransverse(layer.angle) ? 'bg-[#c68642]' : 'bg-[#e8d4b8]'" :style="{ flex: layer.thickness + ' 1 0' }">
                                            <span x-text="'L' + (index + 1) + ' (' + Number(layer.thickness).toFixed(0) + 'mm)'"></span>
                                            <span class="opacity-90" x-show="!isTransverse(layer.angle)">
                                                <svg class="mx-auto h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                                            </span>
                                            <span class="opacity-90" x-show="isTransverse(layer.angle)">
                                                <svg class="mx-auto h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5c.333.667 1.333 2 3 2s2.667-1.333 3-2V4M14 4v5c.333.667 1.333 2 3 2s2.667-1.333 3-2V4M8 20h8" /></svg>
                                            </span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <p class="mt-3 text-center text-[10px] font-semibold uppercase tracking-widest text-slate-400">Bottom (inside)</p>

                            <p class="mt-6 text-center text-sm font-semibold text-slate-800">Cross-Laminated Structural Assembly</p>
                            <p class="mx-auto mt-2 max-w-md text-center text-xs italic leading-relaxed text-slate-500">
                                Diagram is schematic (panel thickness scale). Adhesive lines and edge details omitted. 3D orientation: panel normal out of plane.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function layupSpecificationEditor() {
                return {
                    layers: @json($initialLayers)
                    , syncUrl: @json(route('suppliers.layups.sync-layers', [$supplier -> supplier_id, $layup -> layup_id]))
                    , csrf: @json(csrf_token())
                    , saving: false
                    , dragFrom: null,

                    totalThickness() {
                        return this.layers.reduce((s, l) => s + (Number(l.thickness) || 0), 0);
                    },

                    formatMm(n) {
                        return Math.round(n) + 'mm';
                    },

                    isTransverse(angle) {
                        const a = Number(angle);
                        return Math.abs(a % 180) > 45;
                    },

                    gradeDotClass(grade) {
                        const g = (grade || '').toUpperCase();
                        if (g.includes('C16') || g.includes('T')) return 'bg-amber-500 ring-2 ring-white';
                        return 'bg-emerald-600 ring-2 ring-white';
                    },

                    normalizeLayer(layer) {
                        if (layer.thickness < 0) layer.thickness = 0;
                        if (layer.width < 0) layer.width = 0;
                    },

                    normalizeAngle(layer) {
                        let a = Number(layer.angle);
                        if (Number.isNaN(a)) a = 0;
                        a = ((a % 360) + 360) % 360;
                        layer.angle = a;
                    },

                    addLayer() {
                        this.layers.push({
                            thickness: 20
                            , width: 1200
                            , angle: 90
                            , grade: 'C16'
                        });
                    },

                    removeLayer(index) {
                        if (this.layers.length <= 1) return;
                        this.layers.splice(index, 1);
                    },

                    dragStart(e, index) {
                        this.dragFrom = index;
                        e.dataTransfer.effectAllowed = 'move';
                    },

                    dragEnd() {
                        this.dragFrom = null;
                    },

                    dragOver(e, index) {},

                    drop(toIndex) {
                        const from = this.dragFrom;
                        if (from === null || from === toIndex) return;
                        const item = this.layers.splice(from, 1)[0];
                        this.layers.splice(toIndex, 0, item);
                        this.dragFrom = null;
                    },

                    async saveLayers() {
                        if (this.saving) return;
                        this.saving = true;
                        try {
                            const payload = {
                                layers: this.layers.map((l) => ({
                                    thickness: Number(l.thickness)
                                    , width: Number(l.width)
                                    , angle: Number(l.angle)
                                    , grade: (l.grade || '').trim() || null
                                , }))
                            , };
                            await window.axios.post(this.syncUrl, payload, {
                                headers: {
                                    'X-CSRF-TOKEN': this.csrf
                                    , 'Accept': 'application/json'
                                    , 'Content-Type': 'application/json'
                                , }
                            , });
                            window.location.reload();
                        } catch (err) {
                            console.error(err);
                            alert('Could not save layers. Check the form values and try again.');
                        } finally {
                            this.saving = false;
                        }
                    }
                , };
            }

        </script>
    </x-app-layout>
