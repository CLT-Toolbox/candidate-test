{{-- Conflict Resolution Modal - Modern Design --}}
<x-modal name="conflict-resolution" :show="false" focusable maxWidth="7xl">
    <div class="relative flex max-h-[90vh] w-full flex-col overflow-hidden rounded-xl bg-white">
        {{-- Header --}}
        <div class="flex items-start justify-between border-b border-gray-200 bg-white px-6 py-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-lg font-semibold text-gray-900">
                        {{ __('Conflict Resolution: Import') }}
                    </h2>
                    <span
                        class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">
                        {{ __('Needs Review') }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Please review discrepancies between incoming data and existing records.') }}
                </p>
            </div>
            <button @click="cancelImport"
                class="ml-4 rounded-lg p-1 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="flex flex-1 overflow-hidden">
            {{-- Left Sidebar --}}
            <div class="w-64 flex-shrink-0 overflow-y-auto border-r border-gray-200 bg-gray-50">
                <div class="px-4 pb-2 pt-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-semibold text-gray-800">
                                {{ __('Conflicting Layups') }} (<span
                                    x-text="conflicts.filter(c => !c.resolved).length"></span>)
                            </span>
                        </div>
                    </div>

                    <div class="space-y-1 px-3 pb-2">
                        <template x-for="(conflict, index) in conflicts" :key="index">
                            <button @click="currentConflictIndex = index"
                                :class="{
                                    'border-2 border-green-600 bg-green-50': conflict.resolved,
                                    'border-2 border-orange-400 bg-orange-50': currentConflictIndex === index && !
                                        conflict.resolved,
                                    'border-2 border-transparent hover:bg-gray-100': currentConflictIndex !== index && !
                                        conflict.resolved
                                }"
                                class="relative w-full rounded-lg px-3 py-2.5 text-left transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900" x-text="conflict.name"></p>
                                        <p class="mt-0.5 text-xs text-gray-500"
                                            x-text="`${conflict.layerConflicts?.length || 0} layer diff.`"></p>
                                    </div>
                                    <span x-show="!conflict.resolved"
                                        class="h-2.5 w-2.5 flex-shrink-0 rounded-full bg-red-600"></span>
                                    <span x-show="conflict.resolved" class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </span>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Main Content --}}
                <div class="flex-1 overflow-y-auto bg-gray-50 p-6">
                    <template x-if="currentConflict">
                        <div>
                            {{-- Content Header --}}
                            <div
                                class="mb-5 flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <h3 class="text-lg font-semibold text-gray-900"
                                        x-text="currentConflict.name + ' Comparison'"></h3>
                                    <span
                                        class="inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-xs font-medium text-orange-800">
                                        <span x-text="currentConflict.layerConflicts?.length || 0"></span>&nbsp;LAYERS
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 text-sm text-gray-500">
                                    <span class="inline-block h-2.5 w-2.5 rounded-full bg-red-600"></span>
                                    {{ __('Differences highlighted in') }} <span
                                        class="font-medium text-red-600">{{ __('Red') }}</span>
                                </div>
                            </div>

                            {{-- Comparison Grid --}}
                            <div class="grid grid-cols-2 gap-5">
                                {{-- Existing Version --}}
                                <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="h-2 w-2 rounded-full bg-gray-600"></span>
                                            <h4 class="text-sm font-semibold text-gray-900">{{ __('Existing Version') }}
                                            </h4>
                                        </div>
                                        <p class="mt-0.5 text-xs text-gray-500">{{ __('In database') }}</p>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm">
                                            <thead>
                                                <tr class="border-b border-gray-100 bg-gray-50">
                                                    <th
                                                        class="px-4 py-2.5 text-left text-xs font-semibold uppercase text-gray-600">
                                                        {{ __('Order') }}</th>
                                                    <th
                                                        class="px-4 py-2.5 text-left text-xs font-semibold uppercase text-gray-600">
                                                        {{ __('Thickness') }}</th>
                                                    <th
                                                        class="px-4 py-2.5 text-left text-xs font-semibold uppercase text-gray-600">
                                                        {{ __('Width') }}</th>
                                                    <th
                                                        class="px-4 py-2.5 text-left text-xs font-semibold uppercase text-gray-600">
                                                        {{ __('Angle') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="(row, ri) in currentConflict.existing?.layers"
                                                    :key="ri">
                                                    <tr class="border-b border-gray-100">
                                                        <td class="px-4 py-2.5 font-medium text-gray-700"
                                                            x-text="row.order"></td>
                                                        <td class="px-4 py-2.5 font-medium"
                                                            :class="hasFieldConflict(row.order, 'thickness') ?
                                                                'bg-red-100 text-red-900 font-bold' : 'text-gray-700'"
                                                            x-text="row.thickness">
                                                        </td>
                                                        <td class="px-4 py-2.5 font-medium"
                                                            :class="hasFieldConflict(row.order, 'width') ?
                                                                'bg-red-100 text-red-900 font-bold' : 'text-gray-700'"
                                                            x-text="row.width">
                                                        </td>
                                                        <td class="px-4 py-2.5 font-medium"
                                                            :class="hasFieldConflict(row.order, 'angle') ?
                                                                'bg-red-100 text-red-900 font-bold' : 'text-gray-700'"
                                                            x-text="row.angle">
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="border-t border-gray-200 px-4 py-3">
                                        <button @click="resolveConflict('keep')"
                                            :class="currentConflict.resolution === 'keep' ?
                                                'bg-green-600 text-white border-green-600 hover:bg-green-700' :
                                                'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                            class="flex w-full items-center justify-center gap-2 rounded-lg border-2 px-4 py-2.5 text-sm font-semibold transition-colors">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4" />
                                            </svg>
                                            {{ __('Keep Existing') }}
                                        </button>
                                    </div>
                                </div>

                                {{-- Importing Version --}}
                                <div class="overflow-hidden rounded-lg border border-red-200 bg-white shadow-sm">
                                    <div class="border-b border-red-200 bg-red-50 px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="h-2 w-2 rounded-full bg-green-600"></span>
                                            <h4 class="text-sm font-semibold text-gray-900">
                                                {{ __('Importing Version') }}</h4>
                                        </div>
                                        <p class="mt-0.5 text-xs text-gray-500">{{ __('From file') }}</p>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm">
                                            <thead>
                                                <tr class="border-b border-gray-100 bg-gray-50">
                                                    <th
                                                        class="px-4 py-2.5 text-left text-xs font-semibold uppercase text-gray-600">
                                                        {{ __('Order') }}</th>
                                                    <th
                                                        class="px-4 py-2.5 text-left text-xs font-semibold uppercase text-gray-600">
                                                        {{ __('Thickness') }}</th>
                                                    <th
                                                        class="px-4 py-2.5 text-left text-xs font-semibold uppercase text-gray-600">
                                                        {{ __('Width') }}</th>
                                                    <th
                                                        class="px-4 py-2.5 text-left text-xs font-semibold uppercase text-gray-600">
                                                        {{ __('Angle') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="(row, ri) in currentConflict.importing?.layers"
                                                    :key="ri">
                                                    <tr class="border-b border-gray-100">
                                                        <td class="px-4 py-2.5 font-medium text-gray-700"
                                                            x-text="row.order"></td>
                                                        <td class="px-4 py-2.5 font-medium"
                                                            :class="hasFieldConflict(row.order, 'thickness') ?
                                                                'bg-red-100 text-red-900 font-bold' : 'text-gray-700'"
                                                            x-text="row.thickness">
                                                        </td>
                                                        <td class="px-4 py-2.5 font-medium"
                                                            :class="hasFieldConflict(row.order, 'width') ?
                                                                'bg-red-100 text-red-900 font-bold' : 'text-gray-700'"
                                                            x-text="row.width">
                                                        </td>
                                                        <td class="px-4 py-2.5 font-medium"
                                                            :class="hasFieldConflict(row.order, 'angle') ?
                                                                'bg-red-100 text-red-900 font-bold' : 'text-gray-700'"
                                                            x-text="row.angle">
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="border-t border-red-200 px-4 py-3">
                                        <button @click="resolveConflict('accept')"
                                            :class="currentConflict.resolution === 'accept' ?
                                                'bg-green-600 text-white hover:bg-green-700' :
                                                'bg-green-600 text-white hover:bg-green-700'"
                                            class="flex w-full items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold transition-colors">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            {{ __('Accept New') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-6 py-3.5">
                <button @click="cancelImport"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-50">
                    {{ __('Cancel Import') }}
                </button>

                <div class="flex items-center gap-6">
                    <button @click="previousConflict()"
                        :class="currentConflictIndex === 0 ? 'text-gray-300 cursor-not-allowed' :
                            'text-gray-600 hover:text-gray-900'"
                        :disabled="currentConflictIndex === 0"
                        class="flex items-center gap-1.5 text-sm font-medium transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7" />
                        </svg>
                        {{ __('Previous') }}
                    </button>

                    <span class="whitespace-nowrap text-sm font-medium text-gray-500">
                        <span x-text="currentConflictIndex + 1"></span> {{ __('of') }} <span
                            x-text="conflicts.length"></span> {{ __('ISSUES') }}
                    </span>

                    <button @click="nextConflictOrConfirm()"
                        :class="allConflictsResolved ? 'text-white bg-green-600 hover:bg-green-700' :
                            'text-gray-600 hover:text-gray-900'"
                        :disabled="false"
                        class="flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium transition-colors">
                        <span
                            x-text="allConflictsResolved ? '{{ __('Confirm Import') }}' : '{{ __('Next') }}'"></span>
                        <svg x-show="!allConflictsResolved" class="h-4 w-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <svg x-show="allConflictsResolved" class="h-4 w-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
</x-modal>
