{{-- Add Layer Modal --}}
<x-ui.modal name="add-layer" title="Add New Layer" maxWidth="lg">
    <form x-data="{
        layer_order: layers.length + 1,
        thickness: '',
        width: '',
        angle: 0,
        grade: 'C24',
        loading: false,
        submit() {
            this.loading = true;
            window.axios.post('{{ route('suppliers.layups.layers.store', [$supplier->id, $layup->id]) }}', {
                layer_order: this.layer_order,
                thickness: this.thickness,
                width: this.width,
                angle: this.angle
            })
            .then(response => {
                window.location.reload();
            })
            .catch(error => {
                alert('Error: ' + (error.response?.data?.message || 'Failed to add layer'));
                this.loading = false;
            });
        }
    }" @submit.prevent="submit" class="p-6 space-y-5">
        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <x-ui.label value="Layer Order" />
                <x-ui.input type="number" x-model="layer_order" required />
            </div>
            <div class="space-y-1.5">
                <x-ui.label value="Grade (Mockup)" />
                <select x-model="grade" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs font-bold text-gray-700 focus:ring-2 focus:ring-brand-500/10 focus:border-brand-500 outline-none transition-all">
                    <option value="C24">C24 (Premium)</option>
                    <option value="C16">C16 (Standard)</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <x-ui.label value="Thickness (mm)" />
                <x-ui.input type="number" x-model="thickness" step="0.5" required placeholder="40" />
            </div>
            <div class="space-y-1.5">
                <x-ui.label value="Width (mm)" />
                <x-ui.input type="number" x-model="width" step="1" required placeholder="1200" />
            </div>
        </div>

        <div class="space-y-1.5">
            <x-ui.label value="Orientation Angle" />
            <div class="flex p-1 bg-gray-100 rounded-xl">
                <button type="button" @click="angle = 0" :class="angle == 0 ? 'bg-white shadow-sm text-gray-900 border-gray-200' : 'text-gray-400 border-transparent'" class="flex-1 py-2 text-[10px] font-bold uppercase tracking-widest rounded-lg transition-all border">
                    0° Longitudinal
                </button>
                <button type="button" @click="angle = 90" :class="angle == 90 ? 'bg-white shadow-sm text-gray-900 border-gray-200' : 'text-gray-400 border-transparent'" class="flex-1 py-2 text-[10px] font-bold uppercase tracking-widest rounded-lg transition-all border">
                    90° Transverse
                </button>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-50">
            <x-ui.button type="button" variant="secondary" @click="show = false">
                Cancel
            </x-ui.button>
            <x-ui.button type="submit" variant="primary" ::disabled="loading">
                <span x-show="!loading" class="flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> Add Layer
                </span>
                <span x-show="loading" class="flex items-center gap-2">
                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Adding...
                </span>
            </x-ui.button>
        </div>
    </form>
</x-ui.modal>

<x-ui.modal name="delete-layer" title="Delete Layer" maxWidth="sm">
    <div x-data="{ 
        id: null,
        loading: false,
        submit() {
            this.loading = true;
            window.axios.delete('{{ route('suppliers.layups.layers.destroy', [$supplier->id, $layup->id, ':id']) }}'.replace(':id', this.id))
            .then(() => window.location.reload())
            .catch(error => {
                alert('Error deleting layer');
                this.loading = false;
            });
        }
    }" @delete-layer.window="id = $event.detail.id" class="p-6">
        <div class="flex flex-col items-center text-center space-y-4">
            <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center border border-red-100 mb-2">
                <i data-lucide="trash-2" class="w-8 h-8"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-sm font-bold text-gray-900">Are you sure?</h3>
                <p class="text-xs text-gray-400 font-medium">This action cannot be undone. This layer will be permanently removed from the specification.</p>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-8">
            <x-ui.button variant="secondary" class="flex-1" @click="show = false">
                Cancel
            </x-ui.button>
            <x-ui.button variant="danger" class="flex-1" @click="submit" ::disabled="loading">
                <span x-show="!loading">Delete</span>
                <i x-show="loading" data-lucide="loader-2" class="w-4 h-4 animate-spin mx-auto"></i>
            </x-ui.button>
        </div>
    </div>
</x-ui.modal>

<x-ui.modal name="edit-layer" title="Edit Layer" maxWidth="lg">
    <form x-data="{
        id: null,
        layer_order: 1,
        thickness: '',
        width: '',
        angle: 0,
        grade: 'C24',
        loading: false,
        submit() {
            this.loading = true;
            window.axios.put('{{ route('suppliers.layups.layers.update', [$supplier->id, $layup->id, ':id']) }}'.replace(':id', this.id), {
                layer_order: this.layer_order,
                thickness: this.thickness,
                width: this.width,
                angle: this.angle
            })
            .then(response => {
                window.location.reload();
            })
            .catch(error => {
                alert('Error updating layer');
                this.loading = false;
            });
        }
    }" @edit-layer.window="
        id = $event.detail.id;
        layer_order = $event.detail.layer_order;
        thickness = $event.detail.thickness;
        width = $event.detail.width;
        angle = $event.detail.angle;
        grade = $event.detail.grade || 'C24';
    " @submit.prevent="submit" class="p-6 space-y-5">
        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <x-ui.label value="Layer Order" />
                <x-ui.input type="number" x-model="layer_order" required />
            </div>
            <div class="space-y-1.5">
                <x-ui.label value="Grade (Mockup)" />
                <select x-model="grade" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs font-bold text-gray-700 focus:ring-2 focus:ring-brand-500/10 focus:border-brand-500 outline-none transition-all">
                    <option value="C24">C24 (Premium)</option>
                    <option value="C16">C16 (Standard)</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <x-ui.label value="Thickness (mm)" />
                <x-ui.input type="number" x-model="thickness" step="0.5" required />
            </div>
            <div class="space-y-1.5">
                <x-ui.label value="Width (mm)" />
                <x-ui.input type="number" x-model="width" step="1" required />
            </div>
        </div>

        <div class="space-y-1.5">
            <x-ui.label value="Orientation Angle" />
            <div class="flex p-1 bg-gray-100 rounded-xl">
                <button type="button" @click="angle = 0" :class="angle == 0 ? 'bg-white shadow-sm text-gray-900 border-gray-200' : 'text-gray-400 border-transparent'" class="flex-1 py-2 text-[10px] font-bold uppercase tracking-widest rounded-lg transition-all border">
                    0° Longitudinal
                </button>
                <button type="button" @click="angle = 90" :class="angle == 90 ? 'bg-white shadow-sm text-gray-900 border-gray-200' : 'text-gray-400 border-transparent'" class="flex-1 py-2 text-[10px] font-bold uppercase tracking-widest rounded-lg transition-all border">
                    90° Transverse
                </button>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-50">
            <x-ui.button type="button" variant="secondary" @click="show = false">
                Cancel
            </x-ui.button>
            <x-ui.button type="submit" variant="primary" ::disabled="loading">
                <span x-show="!loading" class="flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Save Changes
                </span>
                <span x-show="loading" class="flex items-center gap-2">
                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Saving...
                </span>
            </x-ui.button>
        </div>
    </form>
</x-ui.modal>
