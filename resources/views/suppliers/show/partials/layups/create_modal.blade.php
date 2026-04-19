<x-ui.modal name="create-layup" title="Add New CLT Layup">
    <div x-data="{
        form: {
            name: '',
            supplier_id: ''
        },
        errors: {},
        loading: false,
        
        init() {
            this.$on('open-create-layup', (data) => {
                this.form.supplier_id = data.detail.supplier_id || '';
                this.errors = {};
            });
        },

        submit() {
            this.loading = true;
            this.errors = {};

            const url = `/suppliers/${this.form.supplier_id}/layups`;

            axios.post(url, this.form)
                .then(response => {
                    window.location.reload();
                })
                .catch(error => {
                    this.loading = false;
                    if (error.response && error.response.status === 422) {
                        this.errors = error.response.data.errors;
                    }
                });
        }
    }" @create-layup.window="
        form.supplier_id = $event.detail.id; 
        errors = {};
        window.dispatchEvent(new CustomEvent('select-search-update-supplier_id', { detail: { id: $event.detail.id, name: $event.detail.name } }));
    ">
        <form @submit.prevent="submit">
            @csrf

            <div class="space-y-4">
                <div>
                    <x-ui.label for="supplier_id" value="Select Supplier" />
                    <x-ui.select-search 
                        name="supplier_id" 
                        placeholder="Search for a supplier..."
                        :value="$supplier->id ?? null"
                        :label="$supplier->name ?? null"
                    />
                    <x-ui.input-error for="errors.supplier_id" />
                </div>

                <div>
                    <x-ui.label for="layup_name" value="Layup Name" />
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-surface-400 group-focus-within:text-brand-500 transition-colors">
                            <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                        </div>
                        <x-ui.input id="layup_name" name="name" type="text" 
                            class="pl-9" 
                            placeholder="e.g. Standard 3-Ply Wall" 
                            x-model="form.name"
                        />
                    </div>
                    <x-ui.input-error for="errors.name" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-50 mt-6">
                <x-ui.button variant="secondary" type="button" x-on:click="$dispatch('close-modal', 'create-layup')" ::disabled="loading">
                    Cancel
                </x-ui.button>
                <x-ui.button type="submit" ::disabled="loading">
                    <span x-show="!loading">Create Layup</span>
                    <span x-show="loading" style="display: none;" class="flex items-center gap-2">
                        <i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i>
                        Saving...
                    </span>
                </x-ui.button>
            </div>
        </form>
    </div>
</x-ui.modal>
