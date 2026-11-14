<x-app-layout>
    <x-slot name="title">{{ $supplier->name }}</x-slot>
    <x-slot name="subheader">Supplier details</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Suppliers</a>
        <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-primary"><i class="bi bi-pencil me-1" aria-hidden="true"></i> Edit</a>
        <x-confirm-form :action="route('admin.suppliers.destroy', $supplier)"
                        :message="'Delete supplier “'.$supplier->name.'”? This cannot be undone.'"
                        button-class="btn btn-outline-danger" />
    </x-slot>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h2 class="card-title h6 mb-0">Contact information</h2></div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt class="text-muted fw-normal small">Email</dt>
                        <dd>{!! $supplier->email ? '<a href="mailto:'.e($supplier->email).'">'.e($supplier->email).'</a>' : '—' !!}</dd>
                        <dt class="text-muted fw-normal small">Phone</dt>
                        <dd>{{ $supplier->phone ?? '—' }}</dd>
                        <dt class="text-muted fw-normal small">Address</dt>
                        <dd class="mb-0">{!! $supplier->address ? nl2br(e($supplier->address)) : '—' !!}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            @include('partials.product-mini-table', ['products' => $products, 'emptyText' => 'No active products are linked to this supplier.'])
        </div>
    </div>
</x-app-layout>
