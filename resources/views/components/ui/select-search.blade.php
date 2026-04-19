@props([
    'placeholder' => 'Select an option...',
    'name' => '',
    'endpoint' => route('suppliers.index'),
    'value' => null,
    'label' => null,
])

<div x-data="{
    open: false,
    search: '',
    options: [],
    loading: false,
    selectedId: '{{ $value }}',
    selectedLabel: '{{ $label }}',
    
    init() {
        this.$watch('search', value => {
            if (value.length > 1) {
                this.fetchOptions();
            } else if (value.length === 0) {
                this.options = [];
            }
        });

        // Listen for external updates (e.g. from Add/Edit modals)
        window.addEventListener('select-search-update-' + '{{ $name }}', (e) => {
            this.selectedId = e.detail.id;
            this.selectedLabel = e.detail.name;
        });
    },

    fetchOptions() {
        this.loading = true;
        axios.get('{{ $endpoint }}', {
            params: { search: this.search },
            headers: { 'Accept': 'application/json' }
        })
        .then(response => {
            // Adjust based on your API response structure, here assuming ApiResponse trait format
            this.options = response.data.data.data || response.data.data || [];
        })
        .finally(() => {
            this.loading = false;
        });
    },

    select(option) {
        this.selectedId = option.id;
        this.selectedLabel = option.name;
        this.open = false;
        this.search = '';
        this.$dispatch('input', this.selectedId);
        this.$parent.form[this.$el.getAttribute('x-model-field')] = this.selectedId;
    },

    clear() {
        this.selectedId = null;
        this.selectedLabel = null;
        this.$dispatch('input', null);
    }
}" 
class="relative w-full"
@click.away="open = false"
{{ $attributes->whereStartsWith('x-model') }}
x-model-field="{{ $name }}"
>
    <input type="hidden" name="{{ $name }}" :value="selectedId">

    <div 
        @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())"
        class="flex items-center justify-between w-full px-4 py-2 bg-white border border-gray-200 rounded-lg cursor-pointer text-xs transition-all hover:border-brand-300 focus:outline-none focus:ring-1 focus:ring-brand-500"
        :class="open ? 'border-brand-500 ring-1 ring-brand-500' : ''"
    >
        <div class="flex items-center gap-2 truncate">
            <i data-lucide="search" class="w-3.5 h-3.5 text-gray-400"></i>
            <span x-text="selectedLabel || '{{ $placeholder }}'" :class="selectedLabel ? 'text-gray-900 font-semibold' : 'text-gray-400'"></span>
        </div>
        <div class="flex items-center gap-1">
            <button x-show="selectedId" @click.stop="clear()" type="button" class="p-1 hover:bg-gray-100 rounded-md text-gray-400">
                <i data-lucide="x" class="w-3 h-3"></i>
            </button>
            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
        </div>
    </div>

    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden"
        style="display: none;"
    >
        <div class="p-2 border-b border-gray-50 flex items-center gap-2 bg-gray-50/30">
            <i data-lucide="search" class="w-3.5 h-3.5 text-gray-400"></i>
            <input 
                x-ref="searchInput"
                x-model.debounce.300ms="search"
                type="text" 
                placeholder="Type to search..." 
                class="w-full bg-transparent border-none text-xs focus:ring-0 p-1 placeholder-gray-300"
                @keydown.escape="open = false"
            >
            <div x-show="loading" class="animate-spin text-brand-500">
                <i data-lucide="loader-2" class="w-3.5 h-3.5"></i>
            </div>
        </div>

        <div class="max-h-60 overflow-y-auto py-1">
            <template x-for="option in options" :key="option.id">
                <div 
                    @click="select(option)"
                    class="px-4 py-2.5 text-xs hover:bg-brand-50 hover:text-brand-700 cursor-pointer flex items-center justify-between group transition-colors"
                >
                    <span x-text="option.name" :class="selectedId == option.id ? 'font-bold text-brand-600' : 'text-gray-700'"></span>
                    <i x-show="selectedId == option.id" data-lucide="check" class="w-3.5 h-3.5 text-brand-600"></i>
                </div>
            </template>

            <div x-show="options.length === 0 && search.length > 1 && !loading" class="px-4 py-8 text-center">
                <div class="flex flex-col items-center gap-2 text-gray-400">
                    <i data-lucide="database" class="w-6 h-6 opacity-20"></i>
                    <span class="text-[10px] font-medium uppercase tracking-widest">No results found</span>
                </div>
            </div>

            <div x-show="search.length <= 1 && options.length === 0" class="px-4 py-8 text-center">
                <span class="text-[10px] text-gray-400 uppercase tracking-widest font-medium">Type at least 2 characters...</span>
            </div>
        </div>
    </div>
</div>
