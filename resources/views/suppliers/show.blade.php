<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-400 mb-1">
                    <a href="{{ route('suppliers.index') }}" class="hover:underline">Suppliers</a>
                    / {{ $supplier->name }}
                </div>
                <h2 class="text-xl font-semibold text-gray-800">{{ $supplier->name }}</h2>
            </div>
            <div class="flex gap-2">
                <!-- Import -->
                <button x-data @click="$dispatch('open-modal', 'import-supplier')"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm rounded-lg hover:bg-gray-50">
                    Import
                </button>
                <!-- Export -->
                <a href="{{ route('suppliers.export', $supplier) }}"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm rounded-lg hover:bg-gray-50">
                    Export
                </a>
                <!-- Add Layup -->
                <button x-data @click="$dispatch('open-modal', 'create-layup')"
                    class="inline-flex items-center px-4 py-2 bg-green-700 text-white text-sm rounded-lg hover:bg-green-800">
                    + Add Layup
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">{{ session('error') }}</div>
        @endif

        <h3 class="text-lg font-semibold text-gray-700 mb-4">Associated Layups</h3>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Layers</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($supplier->layups as $layup)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $layup->name }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $layup->layers->count() }}</td>
                        <td class="px-6 py-4 text-gray-500 text-sm">{{ $layup->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('suppliers.layups.show', [$supplier, $layup]) }}"
                                    class="text-sm text-green-700 hover:underline font-medium">View</a>
                                <button x-data @click="$dispatch('open-modal', 'edit-layup-{{ $layup->id }}')"
                                    class="text-sm text-blue-600 hover:underline">Edit</button>
                                <form method="POST" action="{{ route('suppliers.layups.destroy', [$supplier, $layup]) }}"
                                    onsubmit="return confirm('Delete this layup?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-sm text-red-500 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Layup Modal -->
                    <x-modal name="edit-layup-{{ $layup->id }}">
                        <form method="POST" action="{{ route('suppliers.layups.update', [$supplier, $layup]) }}" class="p-6">
                            @csrf @method('PATCH')
                            <h2 class="text-lg font-semibold mb-4">Edit Layup</h2>
                            <div class="mb-4">
                                <x-input-label value="Name" />
                                <x-text-input name="name" class="mt-1 block w-full" value="{{ $layup->name }}" required />
                            </div>
                            <div class="flex justify-end gap-2">
                                <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                <x-primary-button>Save</x-primary-button>
                            </div>
                        </form>
                    </x-modal>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400">No layups found. Add one!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Layup Modal -->
    <x-modal name="create-layup">
        <form method="POST" action="{{ route('suppliers.layups.store', $supplier) }}" class="p-6">
            @csrf
            <h2 class="text-lg font-semibold mb-4">Add Layup</h2>
            <div class="mb-4">
                <x-input-label value="Name" />
                <x-text-input name="name" class="mt-1 block w-full" placeholder="e.g. CLT-5-150-L" required />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>
            <div class="flex justify-end gap-2">
                <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button>Create</x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Import Modal -->
    <x-modal name="import-supplier" max-width="lg">
        <div x-data="importHandler({{ $supplier->id }})" class="p-6">
            <h2 class="text-lg font-semibold mb-1">Import Layups</h2>
            <p class="text-sm text-gray-500 mb-4">Upload a JSON file exported from this system.</p>

            <!-- Step 1: Upload -->
            <div x-show="step === 1">
                <input type="file" accept=".json" @change="handleFile" class="block w-full text-sm mb-4 border border-gray-300 rounded-lg p-2" />
                <div class="flex justify-end gap-2">
                    <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                    <button @click="detectConflicts"
                        :disabled="!file"
                        class="px-4 py-2 bg-green-700 text-white text-sm rounded-lg hover:bg-green-800 disabled:opacity-50">
                        Next
                    </button>
                </div>
            </div>

            <!-- Step 2: Choose Strategy (no conflicts) -->
            <div x-show="step === 2 && !hasConflicts">
                <p class="text-sm text-green-700 mb-4">✓ No conflicts detected. Choose import strategy:</p>
                <select x-model="strategy" class="block w-full border border-gray-300 rounded-lg p-2 text-sm mb-4">
                    <option value="overwrite">Overwrite Existing</option>
                    <option value="skip">Skip Conflicts</option>
                    <option value="duplicate">Duplicate Layup</option>
                    <option value="reject">Reject on Conflict</option>
                </select>
                <div class="flex justify-end gap-2">
                    <x-secondary-button @click="step = 1">Back</x-secondary-button>
                    <button @click="submitImport"
                        class="px-4 py-2 bg-green-700 text-white text-sm rounded-lg hover:bg-green-800">
                        Import
                    </button>
                </div>
            </div>

            <!-- Step 3: Conflict Resolution UI -->
            <div x-show="step === 2 && hasConflicts">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-red-600">⚠ Conflicting Layups (<span x-text="conflicts.length"></span>)</span>
                    <div class="flex gap-1">
                        <button @click="prevConflict" :disabled="currentConflict === 0" class="px-2 py-1 text-xs border rounded disabled:opacity-40">↑</button>
                        <button @click="nextConflict" :disabled="currentConflict === conflicts.length - 1" class="px-2 py-1 text-xs border rounded disabled:opacity-40">↓</button>
                    </div>
                </div>

                <!-- Conflict List -->
                <div class="flex gap-4">
                    <!-- Left: list -->
                    <div class="w-48 shrink-0 space-y-2">
                        <template x-for="(conflict, i) in conflicts" :key="i">
                            <div @click="currentConflict = i"
                                :class="currentConflict === i ? 'border-green-600 bg-green-50' : 'border-gray-200'"
                                class="border rounded-lg p-2 cursor-pointer text-sm">
                                <div class="font-medium" x-text="conflict.layup_name"></div>
                                <div class="text-xs text-gray-400">
                                    Conflict in layers
                                    <span x-text="conflict.layers.map(l => l.layer_order).join(' & ')"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Right: comparison -->
                    <div class="flex-1" x-show="conflicts.length > 0">
                        <div class="text-sm font-semibold mb-2" x-text="conflicts[currentConflict]?.layup_name + ' Comparison'"></div>
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Existing -->
                            <div class="border rounded-lg p-3">
                                <div class="text-xs font-semibold text-gray-500 mb-2">📋 Existing Version</div>
                                <table class="w-full text-xs">
                                    <thead><tr class="text-gray-400">
                                        <th class="text-left pb-1">Order</th>
                                        <th class="text-left pb-1">Thick</th>
                                        <th class="text-left pb-1">Width</th>
                                        <th class="text-left pb-1">Angle</th>
                                    </tr></thead>
                                    <tbody>
                                        <template x-for="layer in conflicts[currentConflict]?.layers" :key="layer.layer_order">
                                            <tr :class="layer.diff_fields.length ? 'text-red-500' : 'text-gray-700'">
                                                <td x-text="layer.existing.layer_order"></td>
                                                <td x-text="layer.existing.thickness"></td>
                                                <td x-text="layer.existing.width"></td>
                                                <td x-text="layer.existing.angle"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                                <button @click="resolveConflict('skip')"
                                    class="mt-3 w-full py-1.5 border border-green-700 text-green-700 text-xs rounded-lg hover:bg-green-50">
                                    ↺ Keep Existing
                                </button>
                            </div>
                            <!-- Incoming -->
                            <div class="border border-green-600 rounded-lg p-3">
                                <div class="text-xs font-semibold text-gray-500 mb-2">⬆ Importing Version</div>
                                <table class="w-full text-xs">
                                    <thead><tr class="text-gray-400">
                                        <th class="text-left pb-1">Order</th>
                                        <th class="text-left pb-1">Thick</th>
                                        <th class="text-left pb-1">Width</th>
                                        <th class="text-left pb-1">Angle</th>
                                    </tr></thead>
                                    <tbody>
                                        <template x-for="layer in conflicts[currentConflict]?.layers" :key="layer.layer_order">
                                            <tr :class="layer.diff_fields.length ? 'text-red-500' : 'text-gray-700'">
                                                <td x-text="layer.incoming.layer_order"></td>
                                                <td x-text="layer.incoming.thickness"></td>
                                                <td x-text="layer.incoming.width"></td>
                                                <td x-text="layer.incoming.angle"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                                <button @click="resolveConflict('accept')"
                                    class="mt-3 w-full py-1.5 bg-green-700 text-white text-xs rounded-lg hover:bg-green-800">
                                    ✓ Accept New
                                </button>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="flex items-center justify-between mt-3 text-xs text-gray-500">
                            <button @click="prevConflict" class="hover:text-gray-700">← Previous Conflict</button>
                            <span x-text="(currentConflict + 1) + ' of ' + conflicts.length + ' DISCREPANCIES'"></span>
                            <button @click="nextConflict" class="hover:text-gray-700">Next Conflict →</button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between mt-4">
                    <button @click="$dispatch('close')" class="px-3 py-2 border text-sm rounded-lg hover:bg-gray-50">Cancel Import</button>
                    <button @click="submitManualImport"
                        :disabled="Object.keys(resolutions).length < totalConflictCount"
                        class="px-4 py-2 bg-green-700 text-white text-sm rounded-lg hover:bg-green-800 disabled:opacity-50">
                        Apply Resolutions
                    </button>
                </div>
            </div>
        </div>
    </x-modal>
</x-app-layout>

<script>
function importHandler(supplierId) {
    return {
        step: 1,
        file: null,
        strategy: 'overwrite',
        conflicts: [],
        hasConflicts: false,
        currentConflict: 0,
        resolutions: {},
        totalConflictCount: 0,

        handleFile(e) {
            this.file = e.target.files[0];
        },

        async detectConflicts() {
            const formData = new FormData();
            formData.append('file', this.file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            const res = await fetch(`/suppliers/${supplierId}/detect-conflicts`, {
                method: 'POST',
                body: formData,
            });
            const data = await res.json();
            this.conflicts = data.conflicts;
            this.hasConflicts = data.has_conflicts;
            this.totalConflictCount = data.conflicts.reduce((sum, c) => sum + c.layers.length, 0);
            this.step = 2;
        },

        resolveConflict(decision) {
            const conflict = this.conflicts[this.currentConflict];
            conflict.layers.forEach(layer => {
                const key = conflict.layup_name + '_' + layer.layer_order;
                this.resolutions[key] = decision;
            });
            if (this.currentConflict < this.conflicts.length - 1) {
                this.currentConflict++;
            }
        },

        prevConflict() { if (this.currentConflict > 0) this.currentConflict--; },
        nextConflict() { if (this.currentConflict < this.conflicts.length - 1) this.currentConflict++; },

        async submitImport() {
            const formData = new FormData();
            formData.append('file', this.file);
            formData.append('strategy', this.strategy);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            const res = await fetch(`/suppliers/${supplierId}/import`, { method: 'POST', body: formData });
            if (res.redirected) window.location.href = res.url;
        },

        async submitManualImport() {
            const formData = new FormData();
            formData.append('file', this.file);
            formData.append('strategy', 'manual');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            Object.entries(this.resolutions).forEach(([key, val]) => {
                formData.append(`resolutions[${key}]`, val);
            });
            const res = await fetch(`/suppliers/${supplierId}/import`, { method: 'POST', body: formData });
            if (res.redirected) window.location.href = res.url;
        },
    }
}
</script>
