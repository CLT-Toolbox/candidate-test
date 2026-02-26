{{-- Conflict Resolution Modal --}}
<div x-show="showConflictModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center bg-[#00000050]" @click.self="showConflictModal = false"
    style="display: none;" x-cloak>
    {{-- Modal Content --}}
    <div x-show="showConflictModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="flex w-full max-w-[780px] flex-col overflow-hidden rounded-xl bg-[#FAFAF8] shadow-2xl">
        {{-- Header --}}
        <div class="flex items-start justify-between border-b border-[#E8E5E0] px-6 pb-4 pt-5">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-base font-semibold text-[#1A1A1A]">
                        {{ __('Conflict Resolution: Import') }}
                    </h2>
                    <span class="rounded-full bg-[#FEF3C7] px-2.5 py-0.5 text-[11px] font-medium text-[#92400E]">
                        {{ __('Needs Review') }}
                    </span>
                </div>
                <p class="mt-1 text-[12.5px] text-[#6B6B6B]">
                    {{ __('Please review discrepancies between incoming data and existing records.') }}
                </p>
            </div>
            <button @click="showConflictModal = false"
                class="mt-0.5 rounded-md p-1 text-[#6B6B6B] transition hover:bg-[#E8E5E0] hover:text-[#1A1A1A]">
                <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="flex min-h-0 flex-1">
            {{-- Sidebar --}}
            <div class="w-[190px] shrink-0 border-r border-[#E8E5E0] bg-[#FAFAF8] p-4">
                <button @click="sidebarOpen = !sidebarOpen" class="mb-3 flex w-full items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <svg class="h-[13px] w-[13px] text-[#C0392B]" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="text-[11px] font-semibold uppercase tracking-wide text-[#1A1A1A]">
                            {{ __('Conflicts') }}
                        </span>
                    </div>
                    <svg class="h-[14px] w-[14px] text-[#999] transition-transform"
                        :class="{ 'rotate-180': !sidebarOpen }" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                </button>

                <div x-show="sidebarOpen" x-collapse class="space-y-1">
                    <template x-for="(conflict, index) in conflicts" :key="index">
                        <button @click="currentConflictIndex = index"
                            class="flex w-full items-center gap-2 rounded-md px-2 py-2 text-left transition"
                            :class="currentConflictIndex === index ? 'bg-[#E8E5E0]' : 'hover:bg-[#F5F3EF]'">
                            <svg class="h-[11px] w-[11px] shrink-0 text-[#C0392B]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11.5px] font-medium text-[#1A1A1A]" x-text="conflict.name"></p>
                                <p class="truncate text-[9.5px] text-[#999]"
                                    x-text="`${conflict.layerConflicts?.length || 0} layer diff.`"></p>
                            </div>
                        </button>
                    </template>
                </div>

                <button class="mt-3 flex w-full items-center gap-1.5">
                    <div class="flex items-center gap-1.5">
                        <svg class="h-[13px] w-[13px] text-[#27AE60]" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-[11px] font-semibold uppercase tracking-wide text-[#1A1A1A]">
                            {{ __('Resolved') }}
                        </span>
                    </div>
                </button>

                <div class="mt-1 space-y-1">
                    <template x-for="(conflict, index) in conflicts.filter(c => c.resolved)" :key="index">
                        <div class="flex w-full cursor-default items-center gap-2 rounded-md px-2 py-2 opacity-60">
                            <svg class="h-[11px] w-[11px] shrink-0 text-[#27AE60]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11.5px] font-medium text-[#1A1A1A] line-through" x-text="conflict.name">
                                </p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="flex flex-1 flex-col overflow-hidden">
                <div class="flex-1 overflow-y-auto px-6 py-5">
                    <template x-if="currentConflict">
                        <div>
                            <div class="mb-5 flex items-center justify-between">
                                <h3 class="text-[13px] font-semibold text-[#1A1A1A]" x-text="currentConflict.name"></h3>
                                <span class="text-[11px] text-[#999]"
                                    x-text="`${currentConflict.layerConflicts?.length || 0} Layers`"></span>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                {{-- Existing Version --}}
                                <div class="rounded-xl border border-[#D5D0C6] bg-[#FFFFFF]">
                                    <div
                                        class="flex items-center gap-2 rounded-t-xl border-b border-[#E8E5E0] bg-[#F9F8F6] px-4 py-3">
                                        <h4 class="text-[13px] font-semibold text-[#1A1A1A]">
                                            {{ __('Existing Version') }}
                                        </h4>
                                        <span class="ml-auto h-[9px] w-[9px] rounded-full bg-[#3498DB]"></span>
                                    </div>
                                    <div class="px-4 pb-1 pt-1">
                                        <p class="text-[10.5px] text-[#999]">
                                            {{ __('Current: Database record') }}
                                        </p>
                                    </div>
                                    <div class="px-3 pb-3">
                                        <table class="w-full">
                                            <thead>
                                                <tr
                                                    class="text-[9.5px] font-bold uppercase tracking-wider text-[#999]">
                                                    <th class="px-2 py-2 text-left">Order</th>
                                                    <th class="px-2 py-2 text-center">Thickness<br><span
                                                            class="font-normal normal-case">(MM)</span></th>
                                                    <th class="px-2 py-2 text-center">Width<br><span
                                                            class="font-normal normal-case">(MM)</span></th>
                                                    <th class="px-2 py-2 text-center">Angle<br><span
                                                            class="font-normal normal-case">(°)</span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="row in currentConflict.existing?.layers"
                                                    :key="row.order">
                                                    <tr
                                                        :class="hasFieldConflict(row.order, 'thickness') || hasFieldConflict(row
                                                                .order, 'width') || hasFieldConflict(row.order,
                                                            'angle') ? 'bg-[#FEF6F5]' : ''">
                                                        <td class="px-2 py-[7px] text-[12.5px] text-[#1A1A1A]"
                                                            x-text="row.order"></td>
                                                        <td class="px-2 py-[7px] text-center text-[12.5px]"
                                                            :class="hasFieldConflict(row.order, 'thickness') ?
                                                                'font-bold text-[#C0392B]' : 'text-[#1A1A1A]'"
                                                            x-text="row.thickness"></td>
                                                        <td class="px-2 py-[7px] text-center text-[12.5px]"
                                                            :class="hasFieldConflict(row.order, 'width') ?
                                                                'font-bold text-[#C0392B]' : 'text-[#1A1A1A]'"
                                                            x-text="row.width"></td>
                                                        <td class="px-2 py-[7px] text-center text-[12.5px]"
                                                            :class="hasFieldConflict(row.order, 'angle') ?
                                                                'font-bold text-[#C0392B]' : 'text-[#1A1A1A]'"
                                                            x-text="row.angle"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="border-t border-[#E8E5E0] px-4 py-3">
                                        <button @click="resolveConflict('keep')"
                                            :class="currentConflict.resolution === 'keep' ? 'bg-[#3F7A5C] text-[#FFFFFF]' :
                                                'border border-[#D5D0C6] bg-[#FAFAF8] text-[#1A1A1A] hover:bg-[#F0EDE6]'"
                                            class="flex w-full items-center justify-center gap-2 rounded-lg py-2.5 text-[12.5px] font-medium transition">
                                            <svg class="h-[14px] w-[14px]" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            {{ __('Keep Existing') }}
                                        </button>
                                    </div>
                                </div>

                                {{-- Importing Version --}}
                                <div class="rounded-xl border border-[#C0392B30] bg-[#FFFFFF]">
                                    <div
                                        class="flex items-center gap-2 rounded-t-xl border-b border-[#C0392B20] bg-[#FEF6F5] px-4 py-3">
                                        <h4 class="text-[13px] font-semibold text-[#C0392B]">
                                            {{ __('Importing Version') }}
                                        </h4>
                                        <span class="ml-auto h-[9px] w-[9px] rounded-full bg-[#27AE60]"></span>
                                    </div>
                                    <div class="px-4 pb-1 pt-1">
                                        <p class="text-[10.5px] text-[#999]">
                                            {{ __('Source: From file') }}
                                        </p>
                                    </div>
                                    <div class="px-3 pb-3">
                                        <table class="w-full">
                                            <thead>
                                                <tr
                                                    class="text-[9.5px] font-bold uppercase tracking-wider text-[#999]">
                                                    <th class="px-2 py-2 text-left">Order</th>
                                                    <th class="px-2 py-2 text-center">Thickness<br><span
                                                            class="font-normal normal-case">(MM)</span></th>
                                                    <th class="px-2 py-2 text-center">Width<br><span
                                                            class="font-normal normal-case">(MM)</span></th>
                                                    <th class="px-2 py-2 text-center">Angle<br><span
                                                            class="font-normal normal-case">(°)</span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="row in currentConflict.importing?.layers"
                                                    :key="row.order">
                                                    <tr
                                                        :class="hasFieldConflict(row.order, 'thickness') || hasFieldConflict(row
                                                                .order, 'width') || hasFieldConflict(row.order,
                                                            'angle') ? 'bg-[#FEF6F5]' : ''">
                                                        <td class="px-2 py-[7px] text-[12.5px] text-[#1A1A1A]"
                                                            x-text="row.order"></td>
                                                        <td class="px-2 py-[7px] text-center text-[12.5px]"
                                                            :class="hasFieldConflict(row.order, 'thickness') ?
                                                                'font-bold text-[#C0392B]' : 'text-[#1A1A1A]'"
                                                            x-text="row.thickness"></td>
                                                        <td class="px-2 py-[7px] text-center text-[12.5px]"
                                                            :class="hasFieldConflict(row.order, 'width') ?
                                                                'font-bold text-[#C0392B]' : 'text-[#1A1A1A]'"
                                                            x-text="row.width"></td>
                                                        <td class="px-2 py-[7px] text-center text-[12.5px]"
                                                            :class="hasFieldConflict(row.order, 'angle') ?
                                                                'font-bold text-[#C0392B]' : 'text-[#1A1A1A]'"
                                                            x-text="row.angle"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="border-t border-[#C0392B20] px-4 py-3">
                                        <button @click="resolveConflict('accept')" :class="currentConflict.resolution === 'accept' ? 'bg-[#3F7A5C] text-[#FFFFFF]' :
                                                'border border-[#D5D0C6] bg-[#FAFAF8] text-[#1A1A1A] hover:bg-[#F0EDE6]'"
                                            class="flex w-full items-center justify-center gap-2 rounded-lg py-2.5 text-[12.5px] font-medium  transition">
                                            <svg class="h-[14px] w-[14px]" fill="none" stroke="currentColor"
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

                {{-- Footer --}}
                <div class="flex items-center justify-between border-t border-[#E8E5E0] px-5 py-3">
                    <button @click="showConflictModal = false"
                        class="rounded-lg border border-[#D5D0C6] bg-[#FAFAF8] px-4 py-2 text-[12px] font-medium text-[#1A1A1A] transition hover:bg-[#F0EDE6]">
                        {{ __('Cancel Import') }}
                    </button>

                    <div class="flex items-center gap-5">
                        <button @click="previousConflict()" :disabled="currentConflictIndex === 0"
                            :class="currentConflictIndex === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:text-[#1A1A1A]'"
                            class="flex items-center gap-1 text-[12px] font-medium text-[#555] transition">
                            <svg class="h-[13px] w-[13px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            {{ __('Previous Conflict') }}
                        </button>
                        <span class="text-[11px] font-medium text-[#999]"
                            x-text="`${currentConflictIndex + 1} of ${conflicts.filter(c => !c.resolved).length} DISCREPANCIES`"></span>
                        <button @click="nextConflictOrConfirm()" :disabled="false"
                            :class="allConflictsResolved ? 'hover:text-[#1A1A1A] text-[#1A1A1A]' :
                                'hover:text-[#1A1A1A] text-[#555]'"
                            class="flex items-center gap-1 text-[12px] font-medium transition">
                            <span
                                x-text="allConflictsResolved ? '{{ __('Confirm Import') }}' : '{{ __('Next Conflict') }}'"></span>
                            <svg class="h-[13px] w-[13px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
