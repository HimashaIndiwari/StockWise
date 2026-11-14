{{-- Expects $attentionProducts: low-stock and out-of-stock products. --}}
<div class="card h-100">
    <div class="card-header">
        <h2 class="card-title h6 mb-0"><i class="bi bi-exclamation-triangle me-1" aria-hidden="true"></i> Needs attention</h2>
    </div>
    <div class="card-body p-0">
        @if ($attentionProducts->isEmpty())
            <x-empty-state icon="check2-circle" title="All stock levels are healthy">
                No products are low or out of stock.
            </x-empty-state>
        @else
            <ul class="list-group list-group-flush">
                @foreach ($attentionProducts as $product)
                    <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
                        <div class="text-truncate">
                            <a href="{{ area_route('products.show', $product) }}" class="fw-semibold">{{ $product->name }}</a>
                            <div class="small text-muted">{{ $product->sku }} · {{ $product->quantity }} in stock (min {{ $product->min_stock_level }})</div>
                        </div>
                        <x-stock-status :product="$product" />
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
