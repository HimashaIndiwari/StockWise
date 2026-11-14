{{-- Themed override of Laravel's errors::minimal layout used by 401/403/404/419/429/500/503 pages. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · {{ config('app.name', 'StockWise') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body>
<div class="auth-page">
    <a href="{{ url('/') }}" class="auth-brand mb-4">
        <i class="bi bi-box-seam" aria-hidden="true"></i> StockWise
    </a>

    <div class="card auth-card text-center">
        <div class="card-body p-4 p-sm-5">
            <div class="display-5 fw-bold mb-2" style="color: var(--sw-navy)">@yield('code')</div>
            <h1 class="h5 mb-3">@yield('message')</h1>
            <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                <i class="bi bi-house me-1" aria-hidden="true"></i> Back to dashboard
            </a>
        </div>
    </div>
</div>
</body>
</html>
