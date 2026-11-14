<x-app-layout>
    <x-slot name="title">My Profile</x-slot>
    <x-slot name="subheader">Update your name, email address and password.</x-slot>

    <div class="row g-4">
        <div class="col-lg-6">
            @include('profile.partials.update-profile-information-form')
        </div>
        <div class="col-lg-6">
            @include('profile.partials.update-password-form')
        </div>
    </div>
</x-app-layout>
