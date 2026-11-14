<x-app-layout>
    <x-slot name="title">Reports</x-slot>
    <x-slot name="subheader">Stock movement summary from {{ $from->format('Y-m-d') }} to {{ $to->format('Y-m-d') }}.</x-slot>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-2 align-items-end">
                <div class="col-6 col-md-3">
                    <label for="from" class="form-label small mb-1">From</label>
                    <input type="date" name="from" id="from" value="{{ $from->format('Y-m-d') }}" @class(['form-control', 'is-invalid' => $errors->has('from')])>
                </div>
                <div class="col-6 col-md-3">
                    <label for="to" class="form-label small mb-1">To</label>
                    <input type="date" name="to" id="to" value="{{ $to->format('Y-m-d') }}" @class(['form-control', 'is-invalid' => $errors->has('to')])>
                    @error('to')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                </div>
            </form>
        </div>
    </div>

    @php($range = ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')])

    <h2 class="h6 text-uppercase text-muted mb-3">Stock movement summary</h2>
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <x-stat-card :label="'Units received (Stock IN, '.$summary['in_count'].' movements)'" :value="'+'.number_format($summary['in_units'])"
                         icon="box-arrow-in-down" color="success" :href="route('admin.stock-movements.index', ['type' => 'IN'] + $range)" />
        </div>
        <div class="col-6 col-xl-3">
            <x-stat-card :label="'Units issued (Stock OUT, '.$summary['out_count'].' movements)'" :value="'-'.number_format($summary['out_units'])"
                         icon="box-arrow-up" color="danger" :href="route('admin.stock-movements.index', ['type' => 'OUT'] + $range)" />
        </div>
        <div class="col-6 col-xl-3">
            <x-stat-card :label="'Adjustments (+'.$summary['adjust_up'].' / -'.$summary['adjust_down'].' units)'" :value="number_format($summary['adjust_count'])"
                         icon="sliders" color="navy" :href="route('admin.stock-movements.index', ['type' => 'ADJUSTMENT'] + $range)" />
        </div>
        <div class="col-6 col-xl-3">
            <x-stat-card label="Net stock change" :value="($summary['net_change'] > 0 ? '+' : '').number_format($summary['net_change'])"
                         icon="graph-up-arrow" :color="$summary['net_change'] < 0 ? 'danger' : 'success'" />
        </div>
    </div>

    <h2 class="h6 text-uppercase text-muted mb-3">Current inventory</h2>
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3"><x-stat-card label="Active products" :value="number_format($inventory['products'])" icon="box-seam" /></div>
        <div class="col-6 col-xl-3"><x-stat-card label="Units in stock" :value="number_format($inventory['units'])" icon="stack" color="success" /></div>
        <div class="col-6 col-xl-3"><x-stat-card label="Stock value (purchase price)" :value="number_format($inventory['value'], 2)" icon="cash-stack" /></div>
        <div class="col-6 col-xl-3"><x-stat-card label="Low / out of stock" :value="$lowStock->count().' / '.$outOfStock->count()" icon="exclamation-triangle" color="warning" /></div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            @include('reports._stock-table', ['title' => 'Low-stock report', 'icon' => 'exclamation-triangle', 'rows' => $lowStock, 'status' => 'low_stock', 'empty' => 'No products are low on stock.'])
        </div>
        <div class="col-xl-6">
            @include('reports._stock-table', ['title' => 'Out-of-stock report', 'icon' => 'x-octagon', 'rows' => $outOfStock, 'status' => 'out_of_stock', 'empty' => 'No products are out of stock.'])
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title h6 mb-0"><i class="bi bi-activity me-1" aria-hidden="true"></i> Most active products in period</h2>
        </div>
        <div class="card-body p-0">
            @if ($topMovers->isEmpty())
                <x-empty-state icon="activity" title="No stock movements in this period" />
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr>
                            <th scope="col">Product</th>
                            <th scope="col" class="text-end">Movements</th>
                            <th scope="col" class="text-end">Units IN</th>
                            <th scope="col" class="text-end">Units OUT</th>
                            <th scope="col" class="text-end">Current qty</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($topMovers as $row)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.stock-movements.index', ['product_id' => $row->product_id] + $range) }}" class="fw-semibold">{{ $row->product->name }}</a>
                                    <div class="small text-muted">{{ $row->product->sku }}{{ $row->product->trashed() ? ' · archived' : '' }}</div>
                                </td>
                                <td class="text-end">{{ $row->movements }}</td>
                                <td class="text-end text-success">+{{ number_format($row->in_units) }}</td>
                                <td class="text-end text-danger">-{{ number_format($row->out_units) }}</td>
                                <td class="text-end fw-semibold">{{ number_format($row->product->quantity) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
