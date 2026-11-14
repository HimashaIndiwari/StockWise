@props(['product'])
@php
    $status = $product->stockStatus();
    [$type, $icon] = match ($status) {
        \App\Models\Product::STATUS_OUT_OF_STOCK => ['danger', 'x-circle'],
        \App\Models\Product::STATUS_LOW_STOCK => ['warning', 'exclamation-triangle'],
        default => ['success', 'check-circle'],
    };
@endphp
<x-status-badge :type="$type" :icon="$icon" {{ $attributes }}>{{ \App\Models\Product::STATUSES[$status] }}</x-status-badge>
