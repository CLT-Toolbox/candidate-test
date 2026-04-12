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
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">{{ session('error') }}</div>
        @endif

        <!-- Supplier Info Card -->
        <div class="bg-white rounded-xl border p-6 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $supplier->name }}</h1>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-[#3F7A5C] text-white">
                            Active Partner
                        </span>
                    </div>
                    <p class="text-gray-600 text-sm mt-2">ID: SUP-{{ str_pad($supplier->id, 7, '0', STR_PAD_LEFT) }}</p>
                </div>
                <button x-data @click="$dispatch('open-modal', 'edit-supplier')"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm rounded-lg hover:bg-gray-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Supplier
                </button>
            </div>
        </div>

        <!-- Supplier Details -->
        <div class="bg-white rounded-xl border p-6 mb-8">
            <div class="grid grid-cols-4 gap-0 divide-x divide-gray-200">
                <div class="pr-6">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Primary Contact</p>
                    <p class="text-sm text-gray-900 font-medium">
                        {{ $supplier->primary_contact ?? 'N/A' }}
                    </p>
                </div>
                <div class="px-6">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Location</p>
                    <p class="text-sm text-gray-900 font-medium">
                        {{ $supplier->location ?? 'N/A' }}
                    </p>
                </div>
                <div class="px-6">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Material Certifications</p>
                    <p class="text-sm text-gray-900 font-medium">
                        {{ $supplier->material_certifications ?? 'N/A' }}
                    </p>
                </div>
                <div class="pl-6">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Last Audit Date</p>
                    <p class="text-sm text-gray-900 font-medium">
                        {{ $supplier->last_audit_date ? $supplier->last_audit_date->format('M d, Y') : 'N/A' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Associated Layups Header -->
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Associated Layups</h2>
            <div class="flex gap-2">
                <button x-data @click="$dispatch('open-modal', 'import-supplier')"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 text-sm hover:bg-gray-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12" />
                    </svg>
                    Import
                </button>
                <a href="{{ route('suppliers.export', $supplier) }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 text-sm hover:bg-gray-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export
                </a>
                <button x-data @click="$dispatch('open-modal', 'create-layup')"
                    class="inline-flex items-center px-4 py-2 bg-[#3F7A5C] text-white text-sm rounded-lg hover:bg-green-800 transition font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Layup
                </button>
            </div>
        </div>

        <!-- Layups Table -->
        <div class="bg-white rounded-xl border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Layup ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thickness</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ply Count</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Species/Grade</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revision</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplier->layups as $layup)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('suppliers.layups.show', [$supplier, $layup]) }}" class="text-[#3F7A5C] hover:underline font-medium">
                                        {{ $layup->layup_code ?? 'L-' . str_pad($layup->id, 3, '0', STR_PAD_LEFT) }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $layup->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($layup->layers->sum('thickness'), 0) }}mm</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 text-gray-700 font-bold text-xs">
                                        {{ $layup->layers->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $layup->species_grade ?? $layup->layers->pluck('species_grade')->filter()->unique()->join(' / ') ?: '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $layup->revision ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @php
                                        $statusColors = [
                                            'active'   => 'bg-green-100 text-green-800',
                                            'draft'    => 'bg-yellow-100 text-yellow-800',
                                            'archived' => 'bg-gray-100 text-gray-600',
                                        ];
                                        $statusColor = $statusColors[$layup->status ?? 'draft'] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                        ● {{ ucfirst($layup->status ?? 'draft') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm space-x-2">
                                    <a href="{{ route('suppliers.layups.show', [$supplier, $layup]) }}" class="text-[#3F7A5C] hover:underline">View</a>
                                    <span class="text-gray-300">|</span>
                                    <button x-data @click="$dispatch('open-modal', 'edit-layup-{{ $layup->id }}')" class="text-blue-600 hover:underline">Edit</button>
                                    <span class="text-gray-300">|</span>
                                    <form method="POST" action="{{ route('suppliers.layups.destroy', [$supplier, $layup]) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:underline" onclick="return confirm('Delete this layup?')">Delete</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Layup Modal -->
                            <x-modal name="edit-layup-{{ $layup->id }}">
                                <form method="POST" action="{{ route('suppliers.layups.update', [$supplier, $layup]) }}" class="p-6">
                                    @csrf @method('PATCH')
                                    <h2 class="text-lg font-semibold mb-4">Edit Layup</h2>
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label value="Name" />
                                            <x-text-input name="name" class="mt-1 block w-full" value="{{ $layup->name }}" required />
                                        </div>
                                        <div>
                                            <x-input-label value="Layup Code" />
                                            <x-text-input name="layup_code" class="mt-1 block w-full" value="{{ $layup->layup_code }}" />
                                        </div>
                                        <div>
                                            <x-input-label value="Species/Grade" />
                                            <x-text-input name="species_grade" class="mt-1 block w-full" value="{{ $layup->species_grade }}" placeholder="e.g. Spruce / No. 2" />
                                        </div>
                                        <div>
                                            <x-input-label value="Revision" />
                                            <x-text-input name="revision" class="mt-1 block w-full" value="{{ $layup->revision }}" placeholder="e.g. Rev 1" />
                                        </div>
                                        <div>
                                            <x-input-label value="Status" />
                                            <select name="status" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                                                <option value="draft"    {{ ($layup->status ?? 'draft') === 'draft'    ? 'selected' : '' }}>Draft</option>
                                                <option value="active"   {{ ($layup->status ?? '') === 'active'   ? 'selected' : '' }}>Active</option>
                                                <option value="archived" {{ ($layup->status ?? '') === 'archived' ? 'selected' : '' }}>Archived</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2 mt-6">
                                        <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                        <x-primary-button>Save</x-primary-button>
                                    </div>
                                </form>
                            </x-modal>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                    <p class="text-sm mb-2">No layups found.</p>
                                    <button x-data @click="$dispatch('open-modal', 'create-layup')" class="text-[#3F7A5C] hover:underline text-sm font-medium">+ Create one</button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t bg-white">
                <p class="text-sm text-gray-500">Showing {{ $supplier->layups->count() }} of {{ $supplier->layups->count() }} layups</p>
            </div>
        </div>

        <!-- Edit Supplier Modal -->
        <x-modal name="edit-supplier">
            <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="p-6">
                @csrf @method('PATCH')
                <h2 class="text-lg font-semibold mb-4">Edit Supplier</h2>
                <div class="space-y-4">
                    <div>
                        <x-input-label value="Name" />
                        <x-text-input name="name" class="mt-1 block w-full" value="{{ $supplier->name }}" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="Primary Contact" />
                        <x-text-input name="primary_contact" class="mt-1 block w-full" value="{{ $supplier->primary_contact }}" placeholder="e.g. engineering@nordic.ca" />
                    </div>
                    <div>
                        <x-input-label value="Location" />
                        <x-text-input name="location" class="mt-1 block w-full" value="{{ $supplier->location }}" placeholder="e.g. Montreal, QC, Canada" />
                    </div>
                    <div>
                        <x-input-label value="Material Certifications" />
                        <x-text-input name="material_certifications" class="mt-1 block w-full" value="{{ $supplier->material_certifications }}" placeholder="e.g. SPF No. 1/2, D. Fir-L" />
                    </div>
                    <div>
                        <x-input-label value="Last Audit Date" />
                        <x-text-input name="last_audit_date" type="date" class="mt-1 block w-full" value="{{ $supplier->last_audit_date?->format('Y-m-d') }}" />
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                    <x-primary-button>Save Changes</x-primary-button>
                </div>
            </form>
        </x-modal>

        <!-- Create Layup Modal -->
        <x-modal name="create-layup">
            <form method="POST" action="{{ route('suppliers.layups.store', $supplier) }}" class="p-6">
                @csrf
                <h2 class="text-lg font-semibold mb-4">Add Layup</h2>
                <div class="space-y-4">
                    <div>
                        <x-input-label value="Name" />
                        <x-text-input name="name" class="mt-1 block w-full" placeholder="e.g. CLT-5-150-L" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="Layup Code" />
                        <x-text-input name="layup_code" class="mt-1 block w-full" placeholder="e.g. L-204-A" />
                        <x-input-error :messages="$errors->get('layup_code')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="Species/Grade (Optional)" />
                        <x-text-input name="species_grade" class="mt-1 block w-full" placeholder="e.g. Spruce / No. 2" />
                    </div>
                    <div>
                        <x-input-label value="Revision (Optional)" />
                        <x-text-input name="revision" class="mt-1 block w-full" placeholder="e.g. Rev 1" />
                    </div>
                    <div>
                        <x-input-label value="Status" />
                        <select name="status" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                            <option value="draft">Draft</option>
                            <option value="active">Active</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                    <x-primary-button>Create</x-primary-button>
                </div>
            </form>
        </x-modal>

        <x-modal name="import-supplier" max-width="5xl">
            <div x-data="importHandler('{{ $supplier->id }}')" class="min-h-[500px]">

                <div x-show="step === 1" class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-1">Import Layup Data</h2>
                    <p class="text-sm text-gray-500 mb-6">Upload a JSON file exported from this system.</p>

                    <div class="mb-5 border-2 border-dashed border-gray-300 rounded-xl p-10 text-center cursor-pointer transition hover:border-[#3F7A5C] hover:bg-green-50"
                         @click="$refs.fileInput.click()"
                         @drop.prevent="handleDrop($event)"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         :class="isDragging ? 'border-[#3F7A5C] bg-green-50' : (file ? 'border-[#3F7A5C]' : '')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-3 text-[#3F7A5C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="text-sm font-medium text-gray-700">
                            <span class="text-[#3F7A5C] font-semibold">Click to upload</span> or drag and drop
                        </p>
                        <p class="text-xs text-gray-400 mt-1">CSV or JSON up to 10MB</p>
                        <input type="file" x-ref="fileInput" @change="handleFile($event)" accept=".json" class="hidden" />
                    </div>

                    <!-- File selected info -->
                    <div x-show="file" class="mb-5 flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#3F7A5C] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate" x-text="file ? file.name : ''"></p>
                            <p class="text-xs text-gray-400" x-text="file ? (file.size / 1024).toFixed(1) + ' KB' : ''"></p>
                        </div>
                        <button @click.stop="file = null" class="text-gray-400 hover:text-red-500 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Conflict Resolution Strategy -->
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Conflict Resolution Strategy</label>
                        <div class="relative">
                            <select x-model="strategy" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm appearance-none focus:ring-2 focus:ring-[#3F7A5C] focus:border-transparent bg-white">
                                <option value="skip">Skip conflicts (Default)</option>
                                <option value="overwrite">Overwrite existing</option>
                                <option value="duplicate">Duplicate layup (imported)</option>
                                <option value="reject">Reject entire import</option>
                                <option value="manual">Manual — resolve one by one</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Dry Run -->
                    <div class="mb-6 flex items-start gap-3 p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <input type="checkbox" x-model="dryRun" id="dryRun" class="mt-0.5 rounded border-gray-300 text-[#3F7A5C] focus:ring-[#3F7A5C]">
                        <div>
                            <label for="dryRun" class="text-sm font-medium text-gray-700 cursor-pointer">Run as Dry Run</label>
                            <p class="text-xs text-gray-400 mt-0.5">Simulate the import process without saving changes to the database.</p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 ml-auto shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>

                    <!-- Conflict warning (shown after conflict detection) -->
                    <div x-show="conflictWarning" class="mb-5 flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-red-700">Potential Conflicts Detected</p>
                            <p class="text-xs text-red-600 mt-0.5">
                                <span x-text="conflictCount"></span> Layup(s) differ from current data in the database.
                                <button @click="strategy = 'manual'; step = 2" class="underline font-medium ml-1">View details</button>
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button @click="$dispatch('close')" type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 text-sm hover:bg-gray-50">
                            Cancel
                        </button>
                        <button @click="detectConflicts()"
                                :disabled="!file || isLoading"
                                :class="!file || isLoading ? 'opacity-50 cursor-not-allowed' : ''"
                                type="button"
                                class="px-5 py-2 bg-[#3F7A5C] text-white rounded-lg hover:bg-green-800 flex items-center gap-2 text-sm font-medium transition">
                            <span x-show="!isLoading">Confirm Import</span>
                            <span x-show="isLoading" class="flex items-center gap-1">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </div>

                <div x-show="step === 2" class="flex flex-col" style="height: 620px;">
                    <!-- Header -->
                    <div class="border-b px-6 py-4 flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-lg font-bold text-gray-900">Conflict Resolution: Import</h2>
                                <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-xs font-semibold rounded-full">Needs Review</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-0.5">Please review discrepancies between incoming data and existing records.</p>
                        </div>
                        <button @click="$dispatch('close')" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="flex flex-1 overflow-hidden">
                        <div class="w-56 border-r bg-white overflow-y-auto shrink-0">
                            <div class="p-3">
                                <div class="flex items-center gap-2 mb-3 px-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                        Conflicting Layups (<span x-text="conflicts.length"></span>)
                                    </span>
                                    <div class="ml-auto flex gap-1">
                                        <button @click="prevConflict()" class="p-0.5 hover:bg-gray-100 rounded">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                        </button>
                                        <button @click="nextConflict()" class="p-0.5 hover:bg-gray-100 rounded">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="space-y-1.5">
                                    <template x-for="(conflict, idx) in conflicts" :key="idx">
                                        <button @click="selectConflict(idx)"
                                                :class="currentConflictIdx === idx
                                                    ? 'border-[#3F7A5C] bg-green-50 border'
                                                    : 'border border-gray-200 bg-white hover:bg-gray-50'"
                                                class="w-full px-3 py-2.5 rounded-lg text-left transition relative">
                                            <span class="absolute top-2 right-2 w-2 h-2 rounded-full"
                                                  :class="resolutions[conflict.layup_name] ? 'bg-gray-300' : 'bg-red-500'"></span>
                                            <p class="font-medium text-sm text-gray-900 pr-4" x-text="conflict.layup_name"></p>
                                            <p class="text-xs text-gray-400 mt-0.5"
                                               x-text="resolutions[conflict.layup_name] ? 'Resolved' : 'Conflict in layers ' + conflict.layers.map(l => l.layer_order).join(' & ')"></p>
                                        </button>
                                    </template>
                                </div>

                                <!-- Resolved section -->
                                <template x-if="Object.keys(resolutions).length > 0">
                                    <div class="mt-4">
                                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide px-1 mb-2">Resolved</p>
                                        <template x-for="(val, key) in resolutions" :key="key">
                                            <div class="px-3 py-2 text-sm text-gray-400 line-through flex items-center gap-2">
                                                <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span x-text="key"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="flex-1 flex flex-col overflow-hidden bg-white" x-show="currentConflict !== null">
                            <div class="px-6 py-3 border-b flex items-center justify-between bg-white">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-semibold text-gray-900" x-text="currentConflict ? currentConflict.layup_name + ' Comparison' : ''"></h3>
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full"
                                          x-text="currentConflict ? currentConflict.layers.length + ' LAYERS' : ''"></span>
                                </div>
                                <div class="flex items-center gap-1 text-xs text-red-500 font-medium">
                                    <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>
                                    Differences highlighted in Red
                                </div>
                            </div>

                            <!-- Tables -->
                            <div class="flex-1 overflow-y-auto p-5">
                                <div class="grid grid-cols-2 gap-4 h-full">
                                    <div class="border rounded-xl overflow-hidden flex flex-col">
                                        <div class="px-4 py-3 border-b bg-white flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">Existing Version</p>
                                                <p class="text-xs text-gray-400">Current data in system</p>
                                            </div>
                                            <span class="ml-auto w-2.5 h-2.5 rounded-full bg-gray-300"></span>
                                        </div>
                                        <table class="w-full text-sm flex-1">
                                            <thead class="bg-gray-50 border-b">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Order</th>
                                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Thickness<br><span class="font-normal normal-case">(mm)</span></th>
                                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Width<br><span class="font-normal normal-case">(mm)</span></th>
                                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Angle<br><span class="font-normal normal-case">(°)</span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-if="currentConflict">
                                                    <template x-for="(layer, i) in currentConflict.layers" :key="i">
                                                        <tr :class="layer.diff_fields.length ? 'bg-red-50' : 'hover:bg-gray-50'">
                                                            <td class="px-4 py-2.5 text-gray-700" x-text="layer.existing.layer_order"></td>
                                                            <td class="px-4 py-2.5 font-medium"
                                                                :class="layer.diff_fields.includes('thickness') ? 'text-red-600' : 'text-gray-700'"
                                                                x-text="layer.existing.thickness"></td>
                                                            <td class="px-4 py-2.5 font-medium"
                                                                :class="layer.diff_fields.includes('width') ? 'text-red-600' : 'text-gray-700'"
                                                                x-text="layer.existing.width"></td>
                                                            <td class="px-4 py-2.5 font-medium"
                                                                :class="layer.diff_fields.includes('angle') ? 'text-red-600' : 'text-gray-700'"
                                                                x-text="layer.existing.angle"></td>
                                                        </tr>
                                                    </template>
                                                </template>
                                            </tbody>
                                        </table>
                                        <div class="p-3 border-t mt-auto">
                                            <button @click="recordResolution('skip')" type="button"
                                                    class="w-full py-2 border border-[#3F7A5C] text-[#3F7A5C] text-sm font-medium rounded-lg hover:bg-green-50 transition flex items-center justify-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                Keep Existing
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Importing -->
                                    <div class="border border-[#3F7A5C] rounded-xl overflow-hidden flex flex-col">
                                        <div class="px-4 py-3 border-b bg-white flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#3F7A5C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">Importing Version</p>
                                                <p class="text-xs text-gray-400">From your file</p>
                                            </div>
                                            <span class="ml-auto w-2.5 h-2.5 rounded-full bg-[#3F7A5C]"></span>
                                        </div>
                                        <table class="w-full text-sm flex-1">
                                            <thead class="bg-gray-50 border-b">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Order</th>
                                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Thickness<br><span class="font-normal normal-case">(mm)</span></th>
                                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Width<br><span class="font-normal normal-case">(mm)</span></th>
                                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Angle<br><span class="font-normal normal-case">(°)</span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-if="currentConflict">
                                                    <template x-for="(layer, i) in currentConflict.layers" :key="i">
                                                        <tr :class="layer.diff_fields.length ? 'bg-red-50' : 'hover:bg-gray-50'">
                                                            <td class="px-4 py-2.5 text-gray-700" x-text="layer.incoming.layer_order"></td>
                                                            <td class="px-4 py-2.5 font-medium"
                                                                :class="layer.diff_fields.includes('thickness') ? 'text-red-600' : 'text-gray-700'"
                                                                x-text="layer.incoming.thickness"></td>
                                                            <td class="px-4 py-2.5 font-medium"
                                                                :class="layer.diff_fields.includes('width') ? 'text-red-600' : 'text-gray-700'"
                                                                x-text="layer.incoming.width"></td>
                                                            <td class="px-4 py-2.5 font-medium"
                                                                :class="layer.diff_fields.includes('angle') ? 'text-red-600' : 'text-gray-700'"
                                                                x-text="layer.incoming.angle"></td>
                                                        </tr>
                                                    </template>
                                                </template>
                                            </tbody>
                                        </table>
                                        <div class="p-3 border-t mt-auto">
                                            <button @click="recordResolution('accept')" type="button"
                                                    class="w-full py-2 bg-[#3F7A5C] text-white text-sm font-medium rounded-lg hover:bg-green-800 transition flex items-center justify-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Accept New
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Navigation -->
                            <div class="border-t px-6 py-3 flex items-center justify-between bg-white">
                                <button @click="step = 1" type="button" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm text-gray-600 transition">
                                    Cancel Import
                                </button>
                                <div class="flex items-center gap-4">
                                    <button @click="prevConflict()" class="flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 disabled:opacity-40 transition"
                                            :disabled="currentConflictIdx === 0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                        Previous Conflict
                                    </button>
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                        <span x-text="currentConflictIdx + 1"></span> of <span x-text="conflicts.length"></span> DISCREPANCIES
                                    </span>
                                    <button @click="nextConflict()" class="flex items-center gap-1 text-sm text-[#3F7A5C] hover:text-green-800 disabled:opacity-40 transition font-medium"
                                            :disabled="currentConflictIdx === conflicts.length - 1">
                                        Next Conflict
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="step === 3" class="p-6">
                    <div class="text-center">
                        <div class="mb-4 flex justify-center">
                            <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-[#3F7A5C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Ready to Import</h3>
                        <p class="text-gray-500 text-sm mb-6">
                            <span x-text="file ? file.name : 'File'"></span> is ready to be imported.
                        </p>

                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-6 text-left space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Strategy</span>
                                <span class="font-medium text-gray-900 capitalize" x-text="conflicts.length > 0 ? 'Manual (resolved)' : strategy"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Layups</span>
                                <span class="font-medium text-gray-900" x-text="importData ? importData.layups.length : '-'"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Conflicts</span>
                                <span class="font-medium" :class="conflicts.length > 0 ? 'text-orange-600' : 'text-green-600'" x-text="conflicts.length"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Dry Run</span>
                                <span class="font-medium text-gray-900" x-text="dryRun ? 'Yes (no changes saved)' : 'No'"></span>
                            </div>
                        </div>

                        <div class="flex gap-3 justify-center">
                            <button @click="step = conflicts.length > 0 ? 2 : 1" type="button"
                                    class="px-5 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm text-gray-600 transition">
                                ← Back
                            </button>
                            <button @click="confirmImport()"
                                    :disabled="isLoading"
                                    :class="isLoading ? 'opacity-50 cursor-not-allowed' : ''"
                                    type="button"
                                    class="px-6 py-2 bg-[#3F7A5C] text-white rounded-lg hover:bg-green-800 flex items-center gap-2 text-sm font-medium transition">
                                <span x-show="!isLoading" class="flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Confirm Import
                                </span>
                                <span x-show="isLoading" class="flex items-center gap-1">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Importing...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </x-modal>
    </div>

@push('scripts')
<script>
function importHandler(supplierId) {
    return {
        step: 1,
        file: null,
        isDragging: false,
        strategy: 'skip',
        dryRun: false,
        isLoading: false,
        conflictWarning: false,
        conflictCount: 0,

        importData: null,
        conflicts: [],
        currentConflictIdx: 0,
        resolutions: {},

        get currentConflict() {
            return this.conflicts[this.currentConflictIdx] || null;
        },

        handleFile(event) {
            const files = event.target.files;
            if (files?.length > 0) {
                this.file = files[0];
                this.conflictWarning = false;
            }
        },

        handleDrop(event) {
            this.isDragging = false;
            const files = event.dataTransfer?.files;
            if (files?.length > 0) {
                this.file = files[0];
                this.conflictWarning = false;
            }
        },

        selectConflict(idx) {
            this.currentConflictIdx = idx;
        },

        prevConflict() {
            if (this.currentConflictIdx > 0) this.currentConflictIdx--;
        },

        nextConflict() {
            if (this.currentConflictIdx < this.conflicts.length - 1) this.currentConflictIdx++;
        },

        async detectConflicts() {
            if (!this.file) return;

            this.isLoading = true;
            this.conflictWarning = false;

            const formData = new FormData();
            formData.append('file', this.file);

            try {
                const text = await this.file.text();
                this.importData = JSON.parse(text);

                const response = await fetch(`/suppliers/${supplierId}/detect-conflicts`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    }
                });

                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const data = await response.json();

                if (data.has_conflicts && data.conflicts.length > 0) {
                    this.conflicts = data.conflicts;
                    this.conflictCount = data.conflicts.length;
                    this.currentConflictIdx = 0;
                    this.resolutions = {};
                    this.step = 2;
                } else {
                    this.conflicts = [];
                    this.step = 3;
                }
            } catch (error) {
                alert('Error: ' + error.message);
            } finally {
                this.isLoading = false;
            }
        },

        recordResolution(decision) {
            const conflict = this.currentConflict;

            conflict.layers.forEach(layer => {
                const key = `${conflict.layup_name}_${layer.layer_order}`;
                this.resolutions[key] = decision;
            });

            if (this.currentConflictIdx < this.conflicts.length - 1) {
                this.currentConflictIdx++;
            } else {
                this.step = 3;
            }
        },

        async confirmImport() {
            this.isLoading = true;

            const formData = new FormData();
            formData.append('file', this.file);
            formData.append('dry_run', this.dryRun ? '1' : '0');

            if (this.conflicts.length > 0 && Object.keys(this.resolutions).length > 0) {
                const layerResolutions = {};

                this.conflicts.forEach(conflict => {
                    conflict.layers.forEach(layer => {
                        const key = `${conflict.layup_name}_${layer.layer_order}`;
                        const decision = this.resolutions[key];
                        if (decision) {
                            layerResolutions[key] = decision;
                        }
                    });
                });

                formData.append('strategy', 'manual');
                formData.append('resolutions', JSON.stringify(layerResolutions));
            } else {
                formData.append('strategy', this.strategy);
            }

            try {
                const response = await fetch(`/suppliers/${supplierId}/import`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    }
                });

                if (response.ok) {
                    const responseData = await response.json();
                    console.log('✅ Import success:', responseData);
                    window.location.reload();
                } else {
                    const error = await response.json();
                    alert('Import failed: ' + (error.message || error.error || 'Unknown error'));
                }
            } catch (error) {
                alert('Error importing: ' + error.message);
            } finally {
                this.isLoading = false;
            }
        }
    };
}
</script>
@endpush
</x-app-layout>
