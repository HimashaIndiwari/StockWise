<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title.' · ' : '' }}{{ config('app.name', 'StockWise') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">
    @include('partials.navbar')
    @include('partials.sidebar')

    <main class="app-main" id="main-content">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h1 class="mb-0">{{ $header ?? ($title ?? '') }}</h1>
                        @isset($subheader)
                            <div class="text-muted small mt-1">{{ $subheader }}</div>
                        @endisset
                    </div>
                    @isset($actions)
                        <div class="d-flex flex-wrap gap-2">{{ $actions }}</div>
                    @endisset
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">
                @include('partials.flash-messages')
                {{ $slot }}
            </div>
        </div>
    </main>

    @include('partials.footer')
</div>

{{-- Shared confirmation dialog for destructive actions (see resources/js/app.js). --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="confirmModalLabel">
                    <i class="bi bi-exclamation-triangle text-danger me-1" aria-hidden="true"></i> Please confirm
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" data-confirm-message>Are you sure?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" data-confirm-accept>Yes, continue</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>
