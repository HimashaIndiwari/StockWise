<x-guest-layout>
    <x-slot name="title">Confirm password</x-slot>

    <h1 class="h4 fw-semibold mb-1" style="color: var(--sw-navy)">Confirm your password</h1>
    <p class="text-muted mb-4">This is a secure area. Please confirm your password before continuing.</p>

    <form method="POST" action="{{ route('password.confirm') }}" novalidate>
        @csrf
        <x-form.input name="password" type="password" label="Password" required autocomplete="current-password" />

        <button type="submit" class="btn btn-primary w-100">Confirm</button>
    </form>
</x-guest-layout>
