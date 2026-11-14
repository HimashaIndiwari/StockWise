<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title.' · ' : '' }}{{ config('app.name', 'StockWise') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="auth-page">
    <a href="{{ route('login') }}" class="auth-brand mb-4">
        <i class="bi bi-box-seam" aria-hidden="true"></i> StockWise
    </a>

    <div class="card auth-card">
        <div class="card-body p-4 p-sm-5">
            {{ $slot }}
        </div>
    </div>

    <p class="text-white-50 small mt-4 mb-0">Product Inventory Management System</p>
</div>
</body>
</html>
