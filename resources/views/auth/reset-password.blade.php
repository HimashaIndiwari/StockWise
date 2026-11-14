<x-guest-layout>
    <x-slot name="title">Reset password</x-slot>

    <h1 class="h4 fw-semibold mb-4" style="color: var(--sw-navy)">Choose a new password</h1>

    <form method="POST" action="{{ route('password.store') }}" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-form.input name="email" type="email" label="Email address" :value="$request->email" required autofocus autocomplete="username" />
        <x-form.input name="password" type="password" label="New password" required autocomplete="new-password" />
        <x-form.input name="password_confirmation" type="password" label="Confirm new password" required autocomplete="new-password" />

        <button type="submit" class="btn btn-primary w-100">Reset password</button>
    </form>
</x-guest-layout>
