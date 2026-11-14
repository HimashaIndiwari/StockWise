@php($isAdmin = auth()->user()->isAdmin())
<x-app-layout>
    <x-slot name="title">Stock Movements</x-slot>
    <x-slot name="subheader">
        @if ($filteredProduct)
            History for {{ $filteredProduct->name }} ({{ $filteredProduct->sku }}){{ $filteredProduct->trashed() ? ' — archived' : '' }}
        @else
            {{ $isAdmin ? 'Audit history of every stock change.' : 'Audit history for your products.' }}
        @endif
    </x-slot>
    <x-slot name="actions">
        <a href="{{ area_route('stock-movements.create', $filteredProduct && ! $filteredProduct->trashed() ? ['product_id' => $filteredProduct->id] : []) }}" class="btn btn-success">
            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Record movement
        </a>
    </x-slot>

    @php($hasFilters = (bool) array_filter($filters))

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ area_route('stock-movements.index') }}" class="row g-2 align-items-end" role="search">
                @if ($filteredProduct)
                    <input type="hidden" name="product_id" value="{{ $filteredProduct->id }}">
                @endif
                <div class="col-12 col-md-4 col-xl-3">
                    <label for="q" class="form-label small mb-1">Product</label>
                    <input type="search" name="q" id="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Name or SKU">
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <label for="type" class="form-label small mb-1">Type</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">All types</option>
                        <option value="IN" @selected(($filters['type'] ?? '') === 'IN')>Stock IN</option>
                        <option value="OUT" @selected(($filters['type'] ?? '') === 'OUT')>Stock OUT</option>
                        <option value="ADJUSTMENT" @selected(($filters['type'] ?? '') === 'ADJUSTMENT')>Adjustment</option>
                    </select>
                </div>
                @if ($isAdmin)
                    <div class="col-6 col-md-4 col-xl-2">
                        <label for="user_id" class="form-label small mb-1">Recorded by</label>
                        <select name="user_id" id="user_id" class="form-select">
                            <option value="">Anyone</option>
                            @foreach ($users as $id => $name)
                                <option value="{{ $id }}" @selected((string) ($filters['user_id'] ?? '') === (string) $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-6 col-md-4 col-xl-2">
                    <label for="from" class="form-label small mb-1">From</label>
                    <input type="date" name="from" id="from" value="{{ $filters['from'] ?? '' }}" @class(['form-control', 'is-invalid' => $errors->has('from')])>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <label for="to" class="form-label small mb-1">To</label>
                    <input type="date" name="to" id="to" value="{{ $filters['to'] ?? '' }}" @class(['form-control', 'is-invalid' => $errors->has('to')])>
                </div>
                <div class="col-12 col-md-4 col-xl-1">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                @if ($hasFilters)
                    <div class="col-12">
                        <a href="{{ area_route('stock-movements.index') }}" class="small"><i class="bi bi-x-circle me-1" aria-hidden="true"></i>Clear filters</a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title h6 mb-0">{{ number_format($movements->total()) }} {{ Str::plural('movement', $movements->total()) }}</h2>
        </div>
        <div class="card-body p-0">
            @if ($movements->isEmpty())
                <x-empty-state icon="arrow-left-right" title="No stock movements found">
                    {{ $hasFilters ? 'Try changing or clearing the filters.' : 'Stock IN, OUT and adjustments will appear here.' }}
                </x-empty-state>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Product</th>
                            <th scope="col">Type</th>
                            <th scope="col" class="text-end">Change</th>
                            <th scope="col" class="text-end">Previous</th>
                            <th scope="col" class="text-end">New</th>
                            <th scope="col">Recorded by</th>
                            <th scope="col" class="d-none d-lg-table-cell">Reason</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($movements as $movement)
                            <tr>
                                <td class="small text-nowrap">{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    @if ($movement->product->trashed())
                                        <span class="fw-semibold">{{ $movement->product->name }}</span>
                                        <x-status-badge type="neutral" icon="archive" class="ms-1">Archived</x-status-badge>
                                    @else
                                        <a href="{{ area_route('products.show', $movement->product) }}" class="fw-semibold">{{ $movement->product->name }}</a>
                                    @endif
                                    <div class="small text-muted">{{ $movement->product->sku }}</div>
                                </td>
                                <td><x-movement-type :type="$movement->type" /></td>
                                <td @class(['text-end fw-semibold', 'text-success' => $movement->signedChange() > 0, 'text-danger' => $movement->signedChange() < 0])>
                                    {{ $movement->signedChange() > 0 ? '+' : '' }}{{ $movement->signedChange() }}
                                </td>
                                <td class="text-end">{{ number_format($movement->previous_quantity) }}</td>
                                <td class="text-end">{{ number_format($movement->new_quantity) }}</td>
                                <td class="small">{{ $movement->user->name }}</td>
                                <td class="d-none d-lg-table-cell small">{{ $movement->reason ?? '—' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if ($movements->hasPages())
            <div class="card-footer bg-white">{{ $movements->links() }}</div>
        @endif
    </div>
</x-app-layout>
