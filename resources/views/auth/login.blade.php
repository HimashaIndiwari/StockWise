<x-guest-layout>
    <x-slot name="title">Sign in</x-slot>

    <h1 class="h4 fw-semibold mb-1" style="color: var(--sw-navy)">Welcome back</h1>
    <p class="text-muted mb-4">Sign in to manage your inventory.</p>

    @if (session('status'))
        <div class="alert alert-success small" role="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <x-form.input name="email" type="email" label="Email address" required autofocus autocomplete="username" />
        <x-form.input name="password" type="password" label="Password" required autocomplete="current-password" />

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                <label class="form-check-label" for="remember_me">Remember me</label>
            </div>
            @if (Route::has('password.request'))
                <a class="small" href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-box-arrow-in-right me-1" aria-hidden="true"></i> Log in
        </button>
    </form>

    <p class="text-muted small text-center mt-4 mb-0">Accounts are created by an administrator.</p>
</x-guest-layout>
