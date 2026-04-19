<x-app-layout>
    <x-slot name="header">
        <!-- Top Navigation Bar -->
        <div class=" text-white px-8 py-4">
            <div class="flex items-center justify-between max-w-7xl mx-auto">
                <!-- Logo + Title -->
                <div class="flex items-center gap-3">
                    <div>
                        <div class="font-semibold text-xl">Layup Manager</div>
                        <div class="text-xs text-gray-500 -mt-1">Engineering Admin</div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="p-8 max-w-7xl mx-auto" x-data="{ openImport: false }">
        
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('suppliers.index') }}" class="hover:text-gray-700">Suppliers</a>
            <span class="text-gray-400">›</span>
            <span class="text-gray-800 font-medium">{{ $supplier->name }}</span>
        </div>

        <!-- Supplier Header Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 mb-8">
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center gap-4">
                        <h1 class="text-3xl font-semibold text-gray-900">{{ $supplier->name }}</h1>
                        <span class="px-4 py-1.5 bg-emerald-100 text-emerald-700 text-sm font-medium rounded-2xl">
                            Active Partner
                        </span>
                    </div>
                    <p class="text-gray-500 mt-1">ID: SUP-{{ str_pad($supplier->id, 3, '0', STR_PAD_LEFT) }}</p>
                </div>
                
                <a href="{{ route('suppliers.edit', $supplier->id ?? 1) }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 border border-gray-300 rounded-2xl hover:bg-gray-50 text-sm font-medium">
                    ✏️ Edit Supplier
                </a>
            </div>

        </div>

        <!-- ALERT SECTION -->
        @if(session('success'))
            <div class="mb-6 px-6 py-4 rounded-2xl bg-green-100 text-green-800 border border-green-200">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 px-6 py-4 rounded-2xl bg-red-100 text-red-800 border border-red-200">
                ❌ {{ session('error') }}
            </div>
        @endif

        <!-- Associated Layups Section -->
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-2xl font-semibold text-gray-800">Associated Layups</h2>
            
            <div class="flex items-center gap-3">

                {{-- IMPORT (tetap kalau mau dipakai) --}}
                <button
                    @click="openImport = true"
                    class="flex items-center gap-2 px-5 py-3 border border-gray-300 rounded-2xl text-sm hover:bg-gray-50">
                    📥 Import
                </button>

                {{-- EXPORT CSV --}}
                <a href="{{ route('supplier.export.csv', $supplier->id) }}"
                class="flex items-center gap-2 px-5 py-3 border border-gray-300 rounded-2xl text-sm hover:bg-gray-50">
                    📄 CSV
                </a>

                {{-- EXPORT JSON --}}
                <a href="{{ route('supplier.export.json', $supplier->id) }}"
                class="flex items-center gap-2 px-5 py-3 border border-gray-300 rounded-2xl text-sm hover:bg-gray-50">
                    🧾 JSON
                </a>

                {{-- EXPORT EXCEL --}}
                <a href="{{ route('supplier.export.excel', $supplier->id) }}"
                class="flex items-center gap-2 px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-medium shadow-sm">
                    📊 Excel
                </a>

                {{-- ADD LAYUP --}}
                <a href="{{ route('clt-layups.create', ['supplier_id' => $supplier->id]) }}"
                class="flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-medium shadow-sm">
                    + Add Layup
                </a>

            </div>
        </div>

        <!-- Layups Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">LAYUP ID</th>
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NAME</th>
                        <th class="px-8 py-5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($layups as $layup)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-8 py-6 font-mono text-gray-600">{{ $layup->id }}</td>
                        <td class="px-8 py-6 font-medium">{{ $layup->name }}</td>
                        <td class="px-8 py-6 text-right space-x-2">
                            <a href="{{ route('clt-layups.show', $layup->id) }}" 
                            class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                View
                            </a>
                            <a href="{{ route('clt-layups.edit', $layup->id) }}" 
                               class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">Edit</a>
                            <form action="{{ route('clt-layups.destroy', $layup->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Delete this layup?')" 
                                        class="text-red-600 hover:text-red-700 text-sm font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div x-show="openImport" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            
            <div class="bg-white w-full max-w-lg rounded-2xl shadow-lg p-6">

                <h2 class="text-lg font-semibold mb-4">Import Layup Data</h2>

                <form action="{{ route('suppliers.import', $supplier->id) }}" 
                    method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Upload -->
                    <div class="border-2 border-dashed rounded-xl p-6 text-center mb-4">
                        <input type="file" name="file" class="mb-2">
                        <p class="text-sm text-gray-500">JSON up to 10MB</p>
                    </div>

                    <!-- Strategy -->
                    <div class="mb-4">
                        <label class="text-sm">Conflict Strategy</label>
                        <select name="strategy" class="w-full border rounded-lg px-3 py-2 mt-1">
                            <option value="skip">Skip conflicts</option>
                            <option value="overwrite">Overwrite</option>
                            <option value="duplicate">Duplicate Layup</option>
                            <option value="reject">Reject Import</option>
                            <option value="manual">Manual Resolve</option>
                        </select>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="openImport = false"
                            class="px-4 py-2 border rounded-lg">
                            Cancel
                        </button>

                        <button type="submit"
                            class="px-4 py-2 bg-emerald-600 text-white rounded-lg">
                            Confirm Import
                        </button>
                    </div>
                </form>

            </div>
        </div>

        @if(session('conflicts'))
        <div x-data="conflictHandler(@js(session('conflicts')))">

            <!-- Overlay -->
            <div x-show="open && conflicts.length > 0" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">

                <!-- Modal -->
                <div class="bg-white w-full max-w-2xl rounded-2xl shadow-lg p-6">

                    <!-- Header -->
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Resolve Conflicts</h2>
                        <button @click="open = false">✖</button>
                    </div>

                    <!-- Counter -->
                    <div class="text-sm text-gray-500 mb-4">
                        <span x-text="index + 1"></span> of 
                        <span x-text="conflicts.length"></span> conflicts
                    </div>

                    <!-- Content -->
                    <div class="grid grid-cols-2 gap-6">

                        <!-- EXISTING -->
                        <div class="border rounded-xl p-4">
                            <h3 class="font-semibold mb-2 text-gray-600">Existing</h3>

                            <template x-if="conflicts[index]">
                                <div>
                                    <p>Thickness:
                                        <span :class="conflicts[index].diff.thickness ? 'text-red-500 font-bold' : ''"
                                            x-text="conflicts[index].existing.thickness"></span>
                                    </p>
                                    <p>Width:
                                        <span :class="conflicts[index].diff.width ? 'text-red-500 font-bold' : ''"
                                            x-text="conflicts[index].existing.width"></span>
                                    </p>
                                    <p>Angle:
                                        <span :class="conflicts[index].diff.angle ? 'text-red-500 font-bold' : ''"
                                            x-text="conflicts[index].existing.angle"></span>
                                    </p>
                                </div>
                            </template>
                        </div>

                        <!-- INCOMING -->
                        <div class="border rounded-xl p-4">
                            <h3 class="font-semibold mb-2 text-gray-600">Incoming</h3>

                            <template x-if="conflicts[index]">
                                <div>
                                    <p>Thickness:
                                        <span :class="conflicts[index].diff.thickness ? 'text-green-600 font-bold' : ''"
                                            x-text="conflicts[index].incoming.thickness"></span>
                                    </p>
                                    <p>Width:
                                        <span :class="conflicts[index].diff.width ? 'text-green-600 font-bold' : ''"
                                            x-text="conflicts[index].incoming.width"></span>
                                    </p>
                                    <p>Angle:
                                        <span :class="conflicts[index].diff.angle ? 'text-green-600 font-bold' : ''"
                                            x-text="conflicts[index].incoming.angle"></span>
                                    </p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center mt-6">

                        <!-- Navigation -->
                        <div class="flex gap-2">
                            <button @click="index--" :disabled="index === 0"
                                class="px-3 py-1 border rounded">←</button>

                            <button @click="index++" :disabled="index === conflicts.length - 1"
                                class="px-3 py-1 border rounded">→</button>
                        </div>

                        <!-- Decision -->
                        <div class="flex gap-2">
                            <button
                                @click="resolve('keep')"
                                class="px-4 py-2 bg-gray-200 rounded-lg">
                                Keep Existing
                            </button>

                            <button
                                @click="resolve('accept')"
                                class="px-4 py-2 bg-emerald-600 text-white rounded-lg">
                                Accept Incoming
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        @endif
    </div>

    <script>
    function conflictHandler(conflicts) {
        return {
            conflicts: conflicts ?? [],
            index: 0,
            open: (conflicts ?? []).length > 0,

            async resolve(action) {
                let c = this.conflicts[this.index];

                if (!c) return;

                try {
                    await fetch("{{ route('conflicts.resolve') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            layer_id: c.layer_id,
                            action: action,
                            thickness: c.incoming.thickness,
                            width: c.incoming.width,
                            angle: c.incoming.angle,
                        })
                    });

                    // remove resolved conflict
                    this.conflicts.splice(this.index, 1);

                    if (this.conflicts.length === 0) {
                        this.open = false;
                    } else if (this.index >= this.conflicts.length) {
                        this.index = this.conflicts.length - 1;
                    }

                } catch (e) {
                    alert('Failed to resolve conflict');
                    console.error(e);
                }
            }
        }
    }
    </script>
</x-app-layout> 