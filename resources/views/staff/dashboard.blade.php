<x-app-layout>
    <x-slot name="title">My Dashboard</x-slot>
    <x-slot name="subheader">Overview of the products you manage.</x-slot>
    <x-slot name="actions">
        <a href="{{ route('staff.stock-movements.create') }}" class="btn btn-success"><i class="bi bi-arrow-left-right me-1" aria-hidden="true"></i> Record movement</a>
        <a href="{{ route('staff.products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Add product</a>
    </x-slot>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <x-stat-card label="My products" :value="number_format($stats['products'])" icon="box-seam" color="navy" :href="route('staff.products.index')" />
        </div>
        <div class="col-6 col-xl-3">
            <x-stat-card label="My total stock quantity" :value="number_format($stats['stock_quantity'])" icon="stack" color="success" :href="route('staff.stock-movements.index')" />
        </div>
        <div class="col-6 col-xl-3">
            <x-stat-card label="My low-stock products" :value="number_format($stats['low_stock'])" icon="exclamation-triangle" color="warning"
                         :href="route('staff.products.index', ['status' => 'low_stock'])" />
        </div>
        <div class="col-6 col-xl-3">
            <x-stat-card label="My out-of-stock products" :value="number_format($stats['out_of_stock'])" icon="x-octagon" color="danger"
                         :href="route('staff.products.index', ['status' => 'out_of_stock'])" />
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            @include('partials.recent-movements', ['title' => 'My recent stock movements'])
        </div>
        <div class="col-xl-4">
            @include('partials.attention-products')
        </div>
    </div>
</x-app-layout>
