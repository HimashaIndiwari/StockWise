{{-- "Record movement" panel on the product page. Expects $product. --}}
<div class="card">
    <div class="card-header">
        <h2 class="card-title h6 mb-0"><i class="bi bi-arrow-left-right me-1" aria-hidden="true"></i> Record stock movement</h2>
    </div>
    <div class="card-body">
        <p class="small text-muted mb-3">Current stock: <strong class="text-body">{{ number_format($product->quantity) }}</strong></p>
        <form method="POST" action="{{ area_route('stock-movements.store') }}" novalidate>
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            @include('stock._movement-fields', ['idPrefix' => 'panel-'])
            <button type="submit" class="btn btn-success w-100"><i class="bi bi-check2 me-1" aria-hidden="true"></i> Record movement</button>
        </form>
    </div>
</div>
