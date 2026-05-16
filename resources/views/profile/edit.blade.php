<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">{{ __('Profile') }}</h1>
                <p class="text-gray-600 text-sm mt-1">
                    {{ __('Update your profile information, password, and account security.') }}
                </p>
            </div>

            <div class="max-w-3xl space-y-6">
                <div class="bg-white shadow-md rounded-lg border border-gray-200 p-6 sm:p-8">
                    @include('profile.partials.update-profile-information-form')
                </div>

                <div class="bg-white shadow-md rounded-lg border border-gray-200 p-6 sm:p-8">
                    @include('profile.partials.update-password-form')
                </div>

                <div class="bg-white shadow-md rounded-lg border border-gray-200 p-6 sm:p-8">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
