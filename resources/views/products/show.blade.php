@php($isAdmin = auth()->user()->isAdmin())
<x-app-layout>
    <x-slot name="title">{{ $product->name }}</x-slot>
    <x-slot name="subheader"><code>{{ $product->sku }}</code> · {{ $product->category->name }}</x-slot>
    <x-slot name="actions">
        <a href="{{ area_route('products.index') }}" class="btn btn-light border">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Products
        </a>
        @can('update', $product)
            <a href="{{ area_route('products.edit', $product) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1" aria-hidden="true"></i> Edit
            </a>
        @endcan
        @can('delete', $product)
            <x-confirm-form :action="area_route('products.destroy', $product)"
                            :message="'Delete “'.$product->name.'” permanently? The product and all of its stock history will be removed. This cannot be undone.'"
                            label="Delete" icon="trash" button-class="btn btn-outline-danger" />
        @endcan
    </x-slot>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="card-title h6 mb-0">Product details</h2>
                    <x-stock-status :product="$product" />
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted fw-normal">SKU</dt>
                        <dd class="col-sm-8"><code>{{ $product->sku }}</code></dd>

                        <dt class="col-sm-4 text-muted fw-normal">Category</dt>
                        <dd class="col-sm-8">{{ $product->category->name }}</dd>

                        <dt class="col-sm-4 text-muted fw-normal">Supplier</dt>
                        <dd class="col-sm-8">{{ $product->supplier?->name ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted fw-normal">Purchase price</dt>
                        <dd class="col-sm-8">{{ number_format((float) $product->purchase_price, 2) }}</dd>

                        <dt class="col-sm-4 text-muted fw-normal">Selling price</dt>
                        <dd class="col-sm-8">{{ number_format((float) $product->selling_price, 2) }}</dd>

                        <dt class="col-sm-4 text-muted fw-normal">Available quantity</dt>
                        <dd class="col-sm-8 fw-semibold">{{ number_format($product->quantity) }}</dd>

                        <dt class="col-sm-4 text-muted fw-normal">Minimum stock level</dt>
                        <dd class="col-sm-8">{{ number_format($product->min_stock_level) }}</dd>

                        @if ($isAdmin)
                            <dt class="col-sm-4 text-muted fw-normal">Created by</dt>
                            <dd class="col-sm-8">{{ $product->creator->name }}</dd>
                        @endif

                        <dt class="col-sm-4 text-muted fw-normal">Created</dt>
                        <dd class="col-sm-8">{{ $product->created_at->format('Y-m-d H:i') }}</dd>

                        <dt class="col-sm-4 text-muted fw-normal">Description</dt>
                        <dd class="col-sm-8 mb-0">{!! $product->description ? nl2br(e($product->description)) : '<span class="text-muted">—</span>' !!}</dd>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="card-title h6 mb-0"><i class="bi bi-clock-history me-1" aria-hidden="true"></i> Stock history (latest 10)</h2>
                    <a href="{{ area_route('stock-movements.index', ['product_id' => $product->id]) }}" class="small">Full history</a>
                </div>
                <div class="card-body p-0">
                    @if ($movements->isEmpty())
                        <x-empty-state icon="arrow-left-right" title="No stock movements yet" />
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Type</th>
                                    <th scope="col" class="text-end">Change</th>
                                    <th scope="col" class="text-end">Previous</th>
                                    <th scope="col" class="text-end">New</th>
                                    <th scope="col">By</th>
                                    <th scope="col">Reason</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($movements as $movement)
                                    <tr>
                                        <td class="small text-nowrap">{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                                        <td><x-movement-type :type="$movement->type" /></td>
                                        <td @class(['text-end fw-semibold', 'text-success' => $movement->signedChange() > 0, 'text-danger' => $movement->signedChange() < 0])>
                                            {{ $movement->signedChange() > 0 ? '+' : '' }}{{ $movement->signedChange() }}
                                        </td>
                                        <td class="text-end">{{ $movement->previous_quantity }}</td>
                                        <td class="text-end">{{ $movement->new_quantity }}</td>
                                        <td class="small">{{ $movement->user->name }}</td>
                                        <td class="small">{{ $movement->reason ?? '—' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @if ($product->imageUrl())
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="product-image">
                    </div>
                </div>
            @endif

            @can('manageStock', $product)
                @include('stock._record-form', ['product' => $product])
            @endcan
        </div>
    </div>
</x-app-layout>
