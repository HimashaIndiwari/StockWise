{{-- Compact paginated product list for category/supplier detail pages (admin area). --}}
<div class="card">
    <div class="card-header">
        <h2 class="card-title h6 mb-0">Products ({{ $products->total() }})</h2>
    </div>
    <div class="card-body p-0">
        @if ($products->isEmpty())
            <x-empty-state icon="box-seam" :title="$emptyText" />
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th scope="col">Product</th>
                        <th scope="col">SKU</th>
                        <th scope="col" class="text-end">Qty</th>
                        <th scope="col">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td><a href="{{ route('admin.products.show', $product) }}" class="fw-semibold">{{ $product->name }}</a></td>
                            <td><code>{{ $product->sku }}</code></td>
                            <td class="text-end">{{ number_format($product->quantity) }}</td>
                            <td><x-stock-status :product="$product" /></td>
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
