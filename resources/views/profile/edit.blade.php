<x-app-layout>

    <div class="page-content container-fluid">

        <x-slot name="header">
            <h2 class="fw-semibold fs-4 text-dark">
                {{ __('Profile') }}
            </h2>
        </x-slot>

        <div class="py-4">
            <div class="container max-w-7xl">
                <!-- Update Profile Information -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <div class="max-w-xl mx-auto">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                <!-- Update Password -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <div class="max-w-xl mx-auto">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                <!-- Delete Account -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <div class="max-w-xl mx-auto">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
