{{-- Filter Modal --}}
<x-modal name="filter-modal" maxWidth="md">
    <div class="p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">
            {{ __('Filter Suppliers') }}
        </h2>

        <form method="GET" action="{{ route('suppliers.index') }}">
            {{-- Preserve search parameter if exists --}}
            @if (request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            <div class="space-y-4">
                {{-- Sort By --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Sort By') }}
                    </label>
                    <select name="sort_by"
                        class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>
                            {{ __('Date Created') }}
                        </option>
                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>
                            {{ __('Name') }}
                        </option>
                        <option value="updated_at" {{ request('sort_by') == 'updated_at' ? 'selected' : '' }}>
                            {{ __('Last Updated') }}
                        </option>
                        <option value="id" {{ request('sort_by') == 'id' ? 'selected' : '' }}>
                            {{ __('ID') }}
                        </option>
                    </select>
                </div>

                {{-- Order --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Order') }}
                    </label>
                    <select name="order"
                        class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        <option value="desc" {{ request('order', 'desc') == 'desc' ? 'selected' : '' }}>
                            {{ __('Descending (Newest First)') }}
                        </option>
                        <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>
                            {{ __('Ascending (Oldest First)') }}
                        </option>
                    </select>
                </div>
            </div>

            {{-- Modal Actions --}}
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="$dispatch('close-modal', 'filter-modal')"
                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    {{ __('Cancel') }}
                </button>

                <a href="{{ route('suppliers.index') }}"
                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    {{ __('Reset') }}
                </a>

                <button type="submit"
                    class="rounded-md bg-[#3f7a5c] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#365f4a] focus:outline-none focus:ring-2 focus:ring-[#3f7a5c] focus:ring-offset-2">
                    {{ __('Apply Filter') }}
                </button>
            </div>
        </form>
    </div>
</x-modal>
