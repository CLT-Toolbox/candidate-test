<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between gap-2">
            <div>
                <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Add New Supplier</h1>
                <p class="text-gray-500 dark:text-gary-400 mt-2">Register a new timber partner in the CLT manufacturing network.</p>
            </div>
            <x-link-button href="{{ route('suppliers') }}" class="mt-4">
                <span class="material-symbols-outlined text-xl" style="margin-right: 8px">arrow_back</span>
                {{ __('Back to Suppliers') }}
            </x-link-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="text-gray-900 dark:text-gray-100">
                    <form action="{{ isset($supplier) ? route('suppliers.update', $supplier) : route('suppliers.store') }}" method="POST" class="p-8 space-y-8">
                        <x-input-error :messages="$errors->all()" class="mb-4" />
                        @csrf
                        @if (isset($supplier))
                            @method('PUT')
                        @endif
                        <section>
                            <h2 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-6 border-b border-slate-100 dark:border-slate-800 pb-2">Supplier Information</h2>
                            <div class="flex gap-6">
                                <x-input-label class="w-1/4 m-auto text-sm font-medium text-slate-700 dark:text-slate-300" for="supplier_name">Supplier Name</x-input-label>
                                <x-text-input class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg form-input-focus dark:text-white" id="supplier_name" name="name" placeholder="e.g. Nordic Timber Co." type="text" value="{{ isset($supplier) ? $supplier->name : '' }}" />
                            </div>
                        </section>
                        <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-slate-100 dark:border-slate-800">
                            <x-primary-button class="w-full sm:w-auto bg-primary hover:bg-opacity-90 text-white px-10 py-2.5 rounded-md font-medium shadow-sm transition-all" type="submit">
                                {{ isset($supplier) ? __('Update Supplier') : __('Save Supplier') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
