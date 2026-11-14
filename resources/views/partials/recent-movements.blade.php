{{-- Expects $recentMovements (collection of StockMovement with product + user loaded). --}}
<div class="card h-100">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="card-title h6 mb-0"><i class="bi bi-clock-history me-1" aria-hidden="true"></i> {{ $title ?? 'Recent stock movements' }}</h2>
        <a href="{{ area_route('stock-movements.index') }}" class="small">View all</a>
    </div>
    <div class="card-body p-0">
        @if ($recentMovements->isEmpty())
            <x-empty-state icon="arrow-left-right" title="No stock movements yet">
                Stock IN, OUT and adjustments will appear here.
            </x-empty-state>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Product</th>
                        <th scope="col">Type</th>
                        <th scope="col" class="text-end">Change</th>
                        <th scope="col" class="text-end">New qty</th>
                        <th scope="col">By</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($recentMovements as $movement)
                        <tr>
                            <td class="text-nowrap small">{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @if ($movement->product->trashed())
                                    {{ $movement->product->name }}
                                @else
                                    <a href="{{ area_route('products.show', $movement->product) }}">{{ $movement->product->name }}</a>
                                @endif
                                <div class="small text-muted">{{ $movement->product->sku }}</div>
                            </td>
                            <td><x-movement-type :type="$movement->type" /></td>
                            <td @class(['text-end fw-semibold', 'text-success' => $movement->signedChange() > 0, 'text-danger' => $movement->signedChange() < 0])>
                                {{ $movement->signedChange() > 0 ? '+' : '' }}{{ $movement->signedChange() }}
                            </td>
                            <td class="text-end">{{ $movement->new_quantity }}</td>
                            <td class="small">{{ $movement->user->name }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
