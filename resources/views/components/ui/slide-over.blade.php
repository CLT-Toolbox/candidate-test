@props([
    'name',
    'show' => false,
    'title' => null,
])

<div
    x-data="{
        show: @js($show),
        focusables() {
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
            return [...$el.querySelectorAll(selector)].filter(el => ! el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
            setTimeout(() => firstFocusable()?.focus(), 100);
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="fixed inset-0 z-50 overflow-hidden"
    style="display: {{ $show ? 'block' : 'none' }};"
>
    <!-- Background backdrop -->
    <div
        x-show="show"
        class="fixed inset-0 transform transition-all"
        x-on:click="show = false"
        x-transition:enter="ease-in-out duration-500"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in-out duration-500"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
    </div>

    <!-- Slide-over Panel -->
    <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
        <div
            x-show="show"
            class="w-screen max-w-6xl transform transition-all"
            x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
        >
            <div class="h-full flex flex-col bg-white shadow-2xl overflow-hidden border-l border-slate-100">
                {{-- Header --}}
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-brand-900 text-white rounded-xl flex items-center justify-center shadow-lg shadow-brand-900/20">
                            <i data-lucide="database-zap" class="w-5 h-5 text-brand-100"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900 leading-tight">{{ $title }}</h2>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Import Workspace & Conflict Resolver</p>
                        </div>
                    </div>
                    <button x-on:click="show = false" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-white rounded-xl transition-all">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                {{-- Main Content Space --}}
                <div class="flex-1 overflow-hidden">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
