<x-guest-layout>
    <x-slot name="title">Create account</x-slot>

    <h1 class="h4 fw-semibold mb-1" style="color: var(--sw-navy)">Create account</h1>
    <p class="text-muted mb-4">Register as a staff member to manage your inventory.</p>

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <x-form.input name="name" label="Name" required autofocus autocomplete="name" />
        <x-form.input name="email" type="email" label="Email address" required autocomplete="username" />
        <x-form.input name="password" type="password" label="Password" required autocomplete="new-password" />
        <x-form.input name="password_confirmation" type="password" label="Confirm password" required autocomplete="new-password" />

        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-person-plus me-1" aria-hidden="true"></i> Create account
        </button>
    </form>

    <p class="text-muted small text-center mt-4 mb-0">
        Already have an account? <a href="{{ route('login') }}">Log in</a>
    </p>
</x-guest-layout>
