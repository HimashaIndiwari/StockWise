@php($isAdmin = auth()->user()->isAdmin())
<x-app-layout>
    <x-slot name="title">{{ $isAdmin ? 'Products' : 'My Products' }}</x-slot>
    <x-slot name="subheader">{{ $isAdmin ? 'All products across the inventory.' : 'Products you have created.' }}</x-slot>
    <x-slot name="actions">
        @if ($isAdmin)
            <a href="{{ route('admin.products.archived') }}" class="btn btn-light border">
                <i class="bi bi-archive me-1" aria-hidden="true"></i> Archived
            </a>
        @endif
        <a href="{{ area_route('products.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Add product
        </a>
    </x-slot>

    @php($hasFilters = (bool) array_filter(\Illuminate\Support\Arr::except($filters, ['sort', 'direction'])))

    {{-- Search & filters (GET, so they are bookmarkable and preserved by pagination) --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ area_route('products.index') }}" class="row g-2 align-items-end" role="search">
                <div class="col-12 col-md-4 col-xl-3">
                    <label for="q" class="form-label small mb-1">Search</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" name="q" id="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Name or SKU">
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <label for="category_id" class="form-label small mb-1">Category</label>
                    <select name="category_id" id="category_id" class="form-select">
                        <option value="">All categories</option>
                        @foreach ($categories as $id => $name)
                            <option value="{{ $id }}" @selected((string) ($filters['category_id'] ?? '') === (string) $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <label for="supplier_id" class="form-label small mb-1">Supplier</label>
                    <select name="supplier_id" id="supplier_id" class="form-select">
                        <option value="">All suppliers</option>
                        @foreach ($suppliers as $id => $name)
                            <option value="{{ $id }}" @selected((string) ($filters['supplier_id'] ?? '') === (string) $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <label for="status" class="form-label small mb-1">Stock status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Any status</option>
                        @foreach (\App\Models\Product::STATUSES as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($isAdmin)
                    <div class="col-6 col-md-4 col-xl-2">
                        <label for="created_by" class="form-label small mb-1">Created by</label>
                        <select name="created_by" id="created_by" class="form-select">
                            <option value="">Anyone</option>
                            @foreach ($creators as $id => $name)
                                <option value="{{ $id }}" @selected((string) ($filters['created_by'] ?? '') === (string) $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">
                <div class="col-12 col-md-4 col-xl-1">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                @if ($hasFilters)
                    <div class="col-12">
                        <a href="{{ area_route('products.index') }}" class="small"><i class="bi bi-x-circle me-1" aria-hidden="true"></i>Clear filters</a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title h6 mb-0">{{ number_format($products->total()) }} {{ Str::plural('product', $products->total()) }}</h2>
        </div>
        <div class="card-body p-0">
            @if ($products->isEmpty())
                <x-empty-state icon="box-seam" title="No products found">
                    {{ $hasFilters ? 'Try changing or clearing the filters.' : 'Add your first product to start tracking stock.' }}
                </x-empty-state>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr>
                            <th scope="col"><x-sort-link column="name" :sort="$sort" :direction="$direction">Product</x-sort-link></th>
                            <th scope="col"><x-sort-link column="sku" :sort="$sort" :direction="$direction">SKU</x-sort-link></th>
                            <th scope="col">Category</th>
                            <th scope="col" class="d-none d-lg-table-cell">Supplier</th>
                            <th scope="col" class="text-end"><x-sort-link column="selling_price" :sort="$sort" :direction="$direction">Price</x-sort-link></th>
                            <th scope="col" class="text-end"><x-sort-link column="quantity" :sort="$sort" :direction="$direction">Qty</x-sort-link></th>
                            <th scope="col">Status</th>
                            @if ($isAdmin)
                                <th scope="col" class="d-none d-xl-table-cell">Created by</th>
                            @endif
                            <th scope="col" class="d-none d-md-table-cell"><x-sort-link column="created_at" :sort="$sort" :direction="$direction">Created</x-sort-link></th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($product->imageUrl())
                                            <img src="{{ $product->imageUrl() }}" alt="" class="product-thumb">
                                        @endif
                                        <a href="{{ area_route('products.show', $product) }}" class="fw-semibold">{{ $product->name }}</a>
                                    </div>
                                </td>
                                <td><code>{{ $product->sku }}</code></td>
                                <td>{{ $product->category->name }}</td>
                                <td class="d-none d-lg-table-cell">{{ $product->supplier?->name ?? '—' }}</td>
                                <td class="text-end">{{ number_format((float) $product->selling_price, 2) }}</td>
                                <td class="text-end fw-semibold">{{ number_format($product->quantity) }}</td>
                                <td><x-stock-status :product="$product" /></td>
                                @if ($isAdmin)
                                    <td class="d-none d-xl-table-cell small">{{ $product->creator->name }}</td>
                                @endif
                                <td class="d-none d-md-table-cell small text-nowrap">{{ $product->created_at->format('Y-m-d') }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ area_route('products.show', $product) }}" class="btn btn-sm btn-light border" title="View {{ $product->name }}">
                                        <i class="bi bi-eye" aria-hidden="true"></i><span class="visually-hidden">View {{ $product->name }}</span>
                                    </a>
                                    <a href="{{ area_route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary" title="Edit {{ $product->name }}">
                                        <i class="bi bi-pencil" aria-hidden="true"></i><span class="visually-hidden">Edit {{ $product->name }}</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if ($products->hasPages())
            <div class="card-footer bg-white">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
