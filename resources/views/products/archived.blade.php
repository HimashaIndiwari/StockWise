<x-app-layout>
    <x-slot name="title">Archived products</x-slot>
    <x-slot name="subheader">Products hidden from normal listings. Their stock history is preserved.</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.products.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Active products</a>
    </x-slot>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h2 class="card-title h6 mb-0">{{ number_format($products->total()) }} archived {{ Str::plural('product', $products->total()) }}</h2>
            <x-search-form :action="route('admin.products.archived')" :value="$search" placeholder="Name or SKU" />
        </div>
        <div class="card-body p-0">
            @if ($products->isEmpty())
                <x-empty-state icon="archive" title="No archived products">
                    {{ $search ? 'No archived product matches your search.' : 'Archived products will appear here.' }}
                </x-empty-state>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr>
                            <th scope="col">Product</th>
                            <th scope="col">SKU</th>
                            <th scope="col" class="d-none d-md-table-cell">Category</th>
                            <th scope="col" class="d-none d-lg-table-cell">Created by</th>
                            <th scope="col">Archived</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td class="fw-semibold">{{ $product->name }}</td>
                                <td><code>{{ $product->sku }}</code></td>
                                <td class="d-none d-md-table-cell">{{ $product->category->name }}</td>
                                <td class="d-none d-lg-table-cell small">{{ $product->creator->name }}</td>
                                <td class="small text-nowrap">{{ $product->deleted_at->format('Y-m-d H:i') }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.stock-movements.index', ['product_id' => $product->id]) }}" class="btn btn-sm btn-light border">
                                        <i class="bi bi-clock-history" aria-hidden="true"></i><span class="ms-1 d-none d-sm-inline">History</span>
                                    </a>
                                    <x-confirm-form :action="route('admin.products.restore', $product->id)" method="PATCH"
                                                    :message="'Restore “'.$product->name.'” to the active product list?'"
                                                    icon="arrow-counterclockwise" label="Restore" button-class="btn btn-sm btn-outline-success" />
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if ($products->hasPages())
            <div class="card-footer bg-white">{{ $products->links() }}</div>
        @endif
    </div>
</x-app-layout>
