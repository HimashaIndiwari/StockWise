<x-guest-layout>
    <x-slot name="title">Verify email</x-slot>

    <h1 class="h4 fw-semibold mb-1" style="color: var(--sw-navy)">Verify your email</h1>
    <p class="text-muted mb-4">Please verify your email address using the link we just emailed to you.</p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success small" role="status">A new verification link has been sent to your email address.</div>
    @endif

    <div class="d-flex justify-content-between align-items-center gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Resend verification email</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link">Log out</button>
        </form>
    </div>
</x-guest-layout>
