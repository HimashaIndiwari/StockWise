<x-app-layout>
    <x-slot name="title">Record stock movement</x-slot>
    <x-slot name="subheader">Stock IN, stock OUT or an adjustment after a count.</x-slot>
    <x-slot name="actions">
        <a href="{{ area_route('stock-movements.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Movement history</a>
    </x-slot>

    @if ($products->isEmpty())
        <div class="card">
            <x-empty-state icon="box-seam" title="No products available">
                Create a product first, then record its stock movements.
                <x-slot name="action">
                    <a href="{{ area_route('products.create') }}" class="btn btn-success"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Add product</a>
                </x-slot>
            </x-empty-state>
        </div>
    @else
        <form method="POST" action="{{ area_route('stock-movements.store') }}" novalidate>
            @csrf
            <div class="card" style="max-width: 720px">
                <div class="card-body">
                    <x-form.select name="product_id" label="Product" required placeholder="Select a product"
                                   :value="$selectedProductId"
                                   :options="$products->mapWithKeys(fn ($p) => [$p->id => $p->name.' ('.$p->sku.') — '.$p->quantity.' in stock'])" />
                    @include('stock._movement-fields', ['idPrefix' => ''])
                </div>
                <div class="card-footer bg-white d-flex gap-2">
                    <button type="submit" class="btn btn-success"><i class="bi bi-check2 me-1" aria-hidden="true"></i> Record movement</button>
                    <a href="{{ area_route('stock-movements.index') }}" class="btn btn-light border">Cancel</a>
                </div>
            </div>
        </form>
    @endif
</x-app-layout>
