<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
            <p class="mt-1 text-sm text-gray-500">A quick overview of suppliers, layups, layers, and the latest records.</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('partials.alerts')

            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Suppliers</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-900">{{ $stats['suppliers'] }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Layups</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-900">{{ $stats['layups'] }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Layers</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-900">{{ $stats['layers'] }}</p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Recent suppliers</h3>
                        <p class="mt-1 text-sm text-gray-500">Jump back into the latest supplier records and continue editing.</p>
                    </div>
                    <a href="{{ route('suppliers.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
                        View all suppliers
                    </a>
                </div>

                <div class="mt-6 space-y-3">
                    @forelse ($recentSuppliers as $supplier)
                        <a href="{{ route('suppliers.show', $supplier) }}" class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-4 transition hover:border-gray-400 hover:bg-gray-50">
                            <div>
                                <div class="font-semibold text-gray-900">{{ $supplier->name }}</div>
                                <div class="text-sm text-gray-500">{{ $supplier->layups_count }} layups</div>
                            </div>
                            <span class="text-sm font-medium text-gray-500">Open</span>
                        </a>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-6 py-12 text-center">
                            <p class="text-base font-semibold text-gray-900">No suppliers yet.</p>
                            <p class="mt-1 text-sm text-gray-500">Create one to start managing nested layups and layers.</p>
                            <div class="mt-4">
                                <a href="{{ route('suppliers.create') }}" class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700">
                                    Create supplier
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
