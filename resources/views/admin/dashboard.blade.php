<x-app-layout>
    <x-slot name="title">Admin Dashboard</x-slot>
    <x-slot name="subheader">System-wide inventory overview.</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.stock-movements.create') }}" class="btn btn-success"><i class="bi bi-arrow-left-right me-1" aria-hidden="true"></i> Record movement</a>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Add product</a>
    </x-slot>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-3">
            <x-stat-card label="Total products" :value="number_format($stats['products'])" icon="box-seam" color="navy" :href="route('admin.products.index')" />
        </div>
        <div class="col-6 col-md-4 col-xl-3">
            <x-stat-card label="Total stock quantity" :value="number_format($stats['stock_quantity'])" icon="stack" color="success" :href="route('admin.reports.index')" />
        </div>
        <div class="col-6 col-md-4 col-xl-3">
            <x-stat-card label="Low-stock products" :value="number_format($stats['low_stock'])" icon="exclamation-triangle" color="warning"
                         :href="route('admin.products.index', ['status' => 'low_stock'])" />
        </div>
        <div class="col-6 col-md-4 col-xl-3">
            <x-stat-card label="Out-of-stock products" :value="number_format($stats['out_of_stock'])" icon="x-octagon" color="danger"
                         :href="route('admin.products.index', ['status' => 'out_of_stock'])" />
        </div>
        <div class="col-6 col-md-4 col-xl-3">
            <x-stat-card label="Categories" :value="number_format($stats['categories'])" icon="tags" color="navy" :href="route('admin.categories.index')" />
        </div>
        <div class="col-6 col-md-4 col-xl-3">
            <x-stat-card label="Suppliers" :value="number_format($stats['suppliers'])" icon="truck" color="navy" :href="route('admin.suppliers.index')" />
        </div>
        <div class="col-6 col-md-4 col-xl-3">
            <x-stat-card label="Users" :value="number_format($stats['users'])" icon="people" color="navy" :href="route('admin.users.index')" />
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            @include('partials.recent-movements')
        </div>
        <div class="col-xl-4">
            @include('partials.attention-products')
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title h6 mb-0"><i class="bi bi-bar-chart me-1" aria-hidden="true"></i> Stock by category</h2>
        </div>
        <div class="card-body">
            @php($maxStock = max(1, (int) $stockByCategory->max('stock_total')))
            @forelse ($stockByCategory as $category)
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}">{{ $category->name }}</a>
                        <span class="fw-semibold">{{ number_format((int) $category->stock_total) }} units</span>
                    </div>
                    <div class="progress" role="progressbar" aria-label="{{ $category->name }} stock"
                         aria-valuenow="{{ (int) $category->stock_total }}" aria-valuemin="0" aria-valuemax="{{ $maxStock }}" style="height: .6rem">
                        <div class="progress-bar bg-success" style="width: {{ round(((int) $category->stock_total / $maxStock) * 100) }}%"></div>
                    </div>
                </div>
            @empty
                <x-empty-state icon="tags" title="No categories yet" class="py-3" />
            @endforelse
        </div>
    </div>
</x-app-layout>
