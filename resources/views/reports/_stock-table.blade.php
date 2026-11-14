<div class="card h-100">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="card-title h6 mb-0"><i class="bi bi-{{ $icon }} me-1" aria-hidden="true"></i> {{ $title }} ({{ $rows->count() }})</h2>
        <a href="{{ route('admin.products.index', ['status' => $status]) }}" class="small">Open in products</a>
    </div>
    <div class="card-body p-0">
        @if ($rows->isEmpty())
            <x-empty-state icon="check2-circle" :title="$empty" />
        @else
            <div class="table-responsive" style="max-height: 360px">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th scope="col">Product</th>
                        <th scope="col">Category</th>
                        <th scope="col" class="text-end">Qty</th>
                        <th scope="col" class="text-end">Min</th>
                        <th scope="col" class="d-none d-md-table-cell">Owner</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($rows as $product)
                        <tr>
                            <td>
                                <a href="{{ route('admin.products.show', $product) }}" class="fw-semibold">{{ $product->name }}</a>
                                <div class="small text-muted">{{ $product->sku }}</div>
                            </td>
                            <td class="small">{{ $product->category->name }}</td>
                            <td class="text-end fw-semibold">{{ $product->quantity }}</td>
                            <td class="text-end">{{ $product->min_stock_level }}</td>
                            <td class="d-none d-md-table-cell small">{{ $product->creator->name }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
