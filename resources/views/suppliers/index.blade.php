<x-app-layout>
    <x-slot name="title">Suppliers</x-slot>
    <x-slot name="subheader">Companies that supply your products.</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Add supplier
        </a>
    </x-slot>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h2 class="card-title h6 mb-0">{{ number_format($suppliers->total()) }} {{ Str::plural('supplier', $suppliers->total()) }}</h2>
            <x-search-form :action="route('admin.suppliers.index')" :value="$search" placeholder="Name, email or phone" />
        </div>
        <div class="card-body p-0">
            @if ($suppliers->isEmpty())
                <x-empty-state icon="truck" title="No suppliers found">
                    {{ $search ? 'No supplier matches your search.' : 'Add a supplier to link it to products.' }}
                </x-empty-state>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col" class="d-none d-md-table-cell">Email</th>
                            <th scope="col" class="d-none d-sm-table-cell">Phone</th>
                            <th scope="col" class="text-end">Products</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($suppliers as $supplier)
                            <tr>
                                <td><a href="{{ route('admin.suppliers.show', $supplier) }}" class="fw-semibold">{{ $supplier->name }}</a></td>
                                <td class="d-none d-md-table-cell small">{{ $supplier->email ?? '—' }}</td>
                                <td class="d-none d-sm-table-cell small text-nowrap">{{ $supplier->phone ?? '—' }}</td>
                                <td class="text-end">{{ $supplier->products_count }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil" aria-hidden="true"></i><span class="visually-hidden">Edit {{ $supplier->name }}</span>
                                    </a>
                                    <x-confirm-form :action="route('admin.suppliers.destroy', $supplier)"
                                                    :message="'Delete supplier “'.$supplier->name.'”? This cannot be undone.'"
                                                    :label="'Delete '.$supplier->name" label-class="visually-hidden" />
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if ($suppliers->hasPages())
            <div class="card-footer bg-white">{{ $suppliers->links() }}</div>
        @endif
    </div>
</x-app-layout>
