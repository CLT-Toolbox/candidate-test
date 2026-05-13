<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="page-title">{{ __('Dashboard') }}</h2>
            <p class="page-subtitle mt-1">Overview of your CLT data workspace.</p>
        </div>
    </x-slot>

    <div class="pb-10">
        <div class="page-wrap">
            <div class="app-card fade-rise">
                <div class="text-base">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
