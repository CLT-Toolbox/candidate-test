<x-app-layout>
    <x-slot name="header">
        <p class="page-crumb">Suppliers / {{ $supplier->name }} / Conflict Resolution</p>
    </x-slot>

    <div class="pb-10" x-data="{
        current: 0,
        total: {{ count($pendingConflicts) }},
        resolutions: @js(array_fill(0, count($pendingConflicts), 'keep_existing')),
        go(index) { this.current = index; },
        next() { this.current = Math.min(this.current + 1, this.total - 1); },
        prev() { this.current = Math.max(this.current - 1, 0); },
        setDecision(index, value) { this.resolutions[index] = value; },
        isSelected(index) { return this.current === index; },
        submitting: false
    }">
        <div class="page-wrap fade-rise">
            <div class="app-card p-0 overflow-hidden">
                <div class="panel-divider border-b px-5 py-4">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="page-title page-title-xl">Conflict Resolution: Import Review</h2>
                            <p class="page-subtitle mt-1">Resolve discrepancies between existing and imported data.</p>
                        </div>
                        <span class="conflict-chip">Needs Review</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('suppliers.conflicts.apply', $supplier) }}" @submit="submitting = true">
                    @csrf
                    <div class="grid lg:grid-cols-[280px_1fr] min-h-[520px]">
                        <aside class="panel-divider border-r bg-[var(--card-muted)] p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-muted text-sm font-semibold uppercase tracking-wider">Conflicting Layups ({{ count($pendingConflicts) }})</h3>
                            </div>

                            <div class="space-y-2">
                                @foreach ($pendingConflicts as $index => $conflict)
                                    <button
                                        type="button"
                                        class="conflict-item"
                                        :class="isSelected({{ $index }}) ? 'conflict-item-active' : ''"
                                        @click="go({{ $index }})"
                                    >
                                        <p class="font-semibold">{{ $conflict['layup_name'] }}</p>
                                        <p class="muted-copy text-xs mt-1">Layer Order {{ $conflict['layer_order'] }}</p>
                                        <p class="muted-copy text-xs mt-1">Diff: {{ implode(', ', $conflict['diff_fields']) }}</p>
                                    </button>
                                @endforeach
                            </div>
                        </aside>

                        <section class="p-4 sm:p-5">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="section-title">
                                    Comparison
                                </h3>
                                <p class="muted-copy text-sm">Differences are highlighted in red.</p>
                            </div>

                            <div class="grid gap-4 lg:grid-cols-2">
                                @foreach ($pendingConflicts as $index => $conflict)
                                    <input type="hidden" name="resolutions[{{ $index }}][decision]" :value="resolutions[{{ $index }}]">

                                    <div x-show="isSelected({{ $index }})" class="app-card p-0 overflow-hidden">
                                        <div class="panel-divider border-b px-4 py-3">
                                            <p class="font-semibold">Existing Version</p>
                                            <p class="muted-copy text-xs">Current data in database</p>
                                        </div>
                                        <div class="table-shell">
                                            <table class="data-table">
                                                <thead>
                                                    <tr>
                                                        <th>Order</th>
                                                        <th>Thickness</th>
                                                        <th>Width</th>
                                                        <th>Angle</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{ $conflict['layer_order'] }}</td>
                                                        <td>{{ $conflict['existing']['thickness'] }}</td>
                                                        <td>{{ $conflict['existing']['width'] }}</td>
                                                        <td>{{ $conflict['existing']['angle'] }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="panel-divider border-t p-4">
                                            <button type="button" class="btn-secondary w-full" @click="setDecision({{ $index }}, 'keep_existing')">Keep Existing</button>
                                        </div>
                                    </div>

                                    <div x-show="isSelected({{ $index }})" class="app-card p-0 overflow-hidden">
                                        <div class="panel-divider surface-brand-soft border-b px-4 py-3">
                                            <p class="font-semibold">Importing Version</p>
                                            <p class="muted-copy text-xs">Incoming data from JSON import</p>
                                        </div>
                                        <div class="table-shell">
                                            <table class="data-table">
                                                <thead>
                                                    <tr>
                                                        <th>Order</th>
                                                        <th>Thickness</th>
                                                        <th>Width</th>
                                                        <th>Angle</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{ $conflict['layer_order'] }}</td>
                                                        <td :class="resolutions[{{ $index }}] === 'accept_incoming' ? 'conflict-highlight' : ''">{{ $conflict['incoming']['thickness'] }}</td>
                                                        <td :class="resolutions[{{ $index }}] === 'accept_incoming' ? 'conflict-highlight' : ''">{{ $conflict['incoming']['width'] }}</td>
                                                        <td :class="resolutions[{{ $index }}] === 'accept_incoming' ? 'conflict-highlight' : ''">{{ $conflict['incoming']['angle'] }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="panel-divider border-t p-4">
                                            <button type="button" class="btn-primary w-full" @click="setDecision({{ $index }}, 'accept_incoming')">Accept Incoming</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="panel-divider mt-5 flex items-center justify-between border-t pt-4">
                                <button type="button" class="btn-secondary" @click="prev">Previous Conflict</button>
                                <p class="text-main text-sm font-semibold"> <span x-text="current + 1"></span> of <span x-text="total"></span> discrepancies </p>
                                <button type="button" class="btn-secondary" @click="next">Next Conflict</button>
                            </div>
                        </section>
                    </div>

                    <div class="panel-divider flex flex-wrap justify-between gap-2 border-t px-5 py-4">
                        <a href="{{ route('suppliers.show', $supplier) }}" class="btn-secondary">Cancel Import</a>
                        <button type="submit" class="btn-primary" :disabled="submitting" x-text="submitting ? 'Applying...' : 'Apply Conflict Resolution'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
