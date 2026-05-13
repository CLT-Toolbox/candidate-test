<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title page-title-lg leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="pb-10 pt-6">
        <div class="profile-grid fade-rise">
            <div class="profile-card">
                <div class="profile-section">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="profile-card">
                <div class="profile-section">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="profile-card">
                <div class="profile-section">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
