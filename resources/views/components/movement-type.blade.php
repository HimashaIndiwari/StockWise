@props(['type'])
@php
    [$badge, $icon, $label] = match ($type) {
        \App\Models\StockMovement::TYPE_IN => ['success', 'box-arrow-in-down', 'Stock IN'],
        \App\Models\StockMovement::TYPE_OUT => ['danger', 'box-arrow-up', 'Stock OUT'],
        default => ['navy', 'sliders', 'Adjustment'],
    };
@endphp
<x-status-badge :type="$badge" :icon="$icon" {{ $attributes }}>{{ $label }}</x-status-badge>
