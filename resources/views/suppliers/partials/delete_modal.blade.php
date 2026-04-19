<x-ui.modal name="delete-supplier" title="Delete Supplier" maxWidth="sm">
    <div x-data="{
        id: null,
        name: '',
        loading: false,
        submit() {
            this.loading = true;
            axios.delete(`/suppliers/${this.id}`)
                .then(response => {
                    window.location.reload();
                })
                .catch(error => {
                    this.loading = false;
                });
        }
    }" x-on:delete-supplier.window="id = $event.detail.id; name = $event.detail.name">
        <div class="text-center">
            <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
            </div>
            <h3 class="text-sm font-bold text-surface-900 mb-2">Confirm Delete</h3>
            <p class="text-xs text-surface-500 leading-relaxed mb-6">
                Are you sure you want to delete <span class="font-bold text-surface-900" x-text="name"></span>? This action cannot be undone.
            </p>
        </div>

        <div class="flex flex-col gap-3">
            <x-ui.button variant="danger" class="w-full" x-on:click="submit" ::disabled="loading">
                <span x-show="!loading">Delete Supplier</span>
                <span x-show="loading" style="display: none;" class="flex items-center gap-2">
                    <i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i>
                    Deleting...
                </span>
            </x-ui.button>
            <x-ui.button variant="secondary" class="w-full" x-on:click="$dispatch('close-modal', 'delete-supplier')" ::disabled="loading">
                Cancel
            </x-ui.button>
        </div>
    </div>
</x-ui.modal>
