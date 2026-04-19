<x-ui.modal name="create-supplier" title="Add New Supplier">
    <div x-data="{
        form: {
            name: ''
        },
        errors: {},
        loading: false,
        submit() {
            this.loading = true;
            this.errors = {};

            axios.post('{{ route('suppliers.store') }}', this.form)
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
    }">
        <form @submit.prevent="submit">
            @csrf

            <div>
                <x-ui.label for="name" value="Supplier Name" />
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-surface-400 group-focus-within:text-brand-500 transition-colors">
                        <i data-lucide="building-2" class="w-3.5 h-3.5"></i>
                    </div>
                    <x-ui.input id="name" name="name" type="text" 
                        class="pl-9" 
                        placeholder="e.g. Acme Timber Co." 
                        autofocus 
                        x-model="form.name"
                    />
                </div>
                
                <x-ui.input-error for="errors.name" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-4">
                <x-ui.button variant="secondary" type="button" x-on:click="$dispatch('close-modal', 'create-supplier')" ::disabled="loading">
                    Cancel
                </x-ui.button>
                <x-ui.button type="submit" ::disabled="loading">
                    <span x-show="!loading">Create Supplier</span>
                    <span x-show="loading" style="display: none;" class="flex items-center gap-2">
                        <i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i>
                        Saving...
                    </span>
                </x-ui.button>
            </div>
        </form>
    </div>
</x-ui.modal>
