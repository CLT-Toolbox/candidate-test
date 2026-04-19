<x-app-layout>
    <div class="py-12 bg-gray-50/50 min-h-screen" x-data="{}">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            
            <x-ui.breadcrumb :items="[
                'Suppliers' => route('suppliers.index'),
                $supplier->name => null
            ]" />

            <div class="space-y-8">
                @include('suppliers.show.partials.header')

                @include('suppliers.show.partials.info-grid')

                @include('suppliers.show.partials.layups-table')
            </div>

        </div>
    </div>

    @include('suppliers.show.partials.layups.create_modal')
    @include('suppliers.show.partials.layups.edit_modal')
    @include('suppliers.show.partials.layups.delete_modal')
    @include('suppliers.show.partials.layups.import_modal')
</x-app-layout>
