<x-guest-layout>
    <x-slot name="title">Forgot password</x-slot>

    <h1 class="h4 fw-semibold mb-1" style="color: var(--sw-navy)">Reset your password</h1>
    <p class="text-muted mb-4">Enter your email address and we will send you a password reset link.</p>

    @if (session('status'))
        <div class="alert alert-success small" role="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf
        <x-form.input name="email" type="email" label="Email address" required autofocus />

        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-envelope me-1" aria-hidden="true"></i> Email password reset link
        </button>
    </form>

    <p class="text-center small mt-4 mb-0"><a href="{{ route('login') }}">Back to sign in</a></p>
</x-guest-layout>
