<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="text-sm font-medium text-gray-500">Manual Conflict Resolution</div>
            <h2 class="text-2xl font-semibold leading-tight text-gray-900">{{ $supplier->name }}</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('partials.alerts')

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 border-b border-gray-200 pb-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ count($conflicts) }} conflict(s) detected</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Incoming supplier reference:
                            <span class="font-semibold text-gray-900">{{ $preview['source_supplier_name'] ?: 'Not provided' }}</span>
                        </p>
                    </div>
                    <a href="{{ route('suppliers.show', $supplier) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
                        Back to supplier
                    </a>
                </div>

                @php($oldResolutions = old('resolutions', []))

                <form method="POST" action="{{ route('suppliers.import.resolve', $supplier) }}" class="mt-6 space-y-6" x-data="{ active: 0, total: {{ count($conflicts) }} }">
                    @csrf

                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-gray-50 px-4 py-3">
                        <div class="text-sm text-gray-600">
                            Conflict <span class="font-semibold text-gray-900" x-text="active + 1"></span> of <span class="font-semibold text-gray-900" x-text="total"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-white disabled:cursor-not-allowed disabled:opacity-50" x-on:click="active = Math.max(0, active - 1)" x-bind:disabled="active === 0">
                                Previous
                            </button>
                            <button type="button" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-white disabled:cursor-not-allowed disabled:opacity-50" x-on:click="active = Math.min(total - 1, active + 1)" x-bind:disabled="active === total - 1">
                                Next
                            </button>
                        </div>
                    </div>

                    @foreach ($conflicts as $index => $conflict)
                        @php($selected = $oldResolutions[$conflict['key']] ?? 'keep_existing')

                        <section class="space-y-4" x-show="active === {{ $index }}" x-cloak>
                            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5">
                                <p class="text-sm font-medium text-gray-500">Layup</p>
                                <h4 class="text-lg font-semibold text-gray-900">{{ $conflict['layup_name'] }}</h4>
                                <p class="mt-1 text-sm text-gray-500">Layer order {{ $conflict['layer_order'] }}</p>
                            </div>

                            <div class="grid gap-4 lg:grid-cols-2">
                                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                                    <p class="text-sm font-semibold text-gray-900">Existing version</p>
                                    <div class="mt-4 space-y-3">
                                        @foreach (['thickness', 'width', 'angle'] as $field)
                                            <div class="rounded-xl px-4 py-3 {{ array_key_exists($field, $conflict['differences']) ? 'bg-rose-50 text-rose-900 ring-1 ring-rose-200' : 'bg-gray-50 text-gray-700' }}">
                                                <div class="text-xs font-semibold uppercase tracking-wide">{{ $field }}</div>
                                                <div class="mt-1 text-base font-semibold">{{ $conflict['existing'][$field] }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                                    <p class="text-sm font-semibold text-gray-900">Incoming version</p>
                                    <div class="mt-4 space-y-3">
                                        @foreach (['thickness', 'width', 'angle'] as $field)
                                            <div class="rounded-xl px-4 py-3 {{ array_key_exists($field, $conflict['differences']) ? 'bg-emerald-50 text-emerald-900 ring-1 ring-emerald-200' : 'bg-gray-50 text-gray-700' }}">
                                                <div class="text-xs font-semibold uppercase tracking-wide">{{ $field }}</div>
                                                <div class="mt-1 text-base font-semibold">{{ $conflict['incoming'][$field] }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-gray-200 bg-white p-5">
                                <p class="text-sm font-semibold text-gray-900">Resolution</p>
                                <div class="mt-4 space-y-3">
                                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 px-4 py-3 transition hover:border-gray-400">
                                        <input type="radio" name="resolutions[{{ $conflict['key'] }}]" value="keep_existing" class="mt-1 border-gray-300 text-gray-900 focus:ring-gray-500" @checked($selected === 'keep_existing')>
                                        <span>
                                            <span class="block text-sm font-semibold text-gray-900">Keep existing</span>
                                            <span class="block text-sm text-gray-500">Ignore the incoming change for this layer.</span>
                                        </span>
                                    </label>
                                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 px-4 py-3 transition hover:border-gray-400">
                                        <input type="radio" name="resolutions[{{ $conflict['key'] }}]" value="accept_incoming" class="mt-1 border-gray-300 text-gray-900 focus:ring-gray-500" @checked($selected === 'accept_incoming')>
                                        <span>
                                            <span class="block text-sm font-semibold text-gray-900">Accept incoming</span>
                                            <span class="block text-sm text-gray-500">Overwrite the current layer values with the imported ones.</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </section>
                    @endforeach

                    <div class="flex flex-col gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-gray-500">All selections are submitted together after you finish reviewing the discrepancies.</p>
                        <div class="flex gap-3">
                            <a href="{{ route('suppliers.show', $supplier) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
                                Cancel
                            </a>
                            <button type="submit" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700">
                                Apply resolutions
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
