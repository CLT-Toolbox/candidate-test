<x-ui.modal name="delete-layup" title="Delete CLT Layup">
    <div x-data="{
        id: '',
        name: '',
        loading: false,
        submit() {
            this.loading = true;
            axios.delete(`/layups/${this.id}`)
                .then(response => {
                    window.location.reload();
                })
                .catch(error => {
                    this.loading = false;
                    alert('Failed to delete layup');
                });
        }
    }" @delete-layup.window="id = $event.detail.id; name = $event.detail.name">
        <form @submit.prevent="submit" class="p-6">
            <div class="flex items-start gap-4 mb-6">
                <div class="flex-shrink-0 w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-red-600 ring-1 ring-red-100">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 mb-1">Confirm Deletion</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Are you sure you want to delete <span class="font-bold text-gray-900" x-text="name"></span>? 
                        This action cannot be undone and all associated layers will be permanently removed.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-50">
                <x-ui.button variant="secondary" type="button" x-on:click="$dispatch('close-modal', 'delete-layup')" ::disabled="loading">
                    Cancel
                </x-ui.button>
                <x-ui.button variant="primary" type="submit" class="bg-red-600 hover:bg-red-700 border-red-200" ::disabled="loading">
                    <span x-show="!loading">Delete Layup</span>
                    <span x-show="loading" style="display: none;" class="flex items-center gap-2">
                        <i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i>
                        Deleting...
                    </span>
                </x-ui.button>
            </div>
        </form>
    </div>
</x-ui.modal>
