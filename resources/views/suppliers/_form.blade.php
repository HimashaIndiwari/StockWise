<div class="card" style="max-width: 720px">
    <div class="card-body">
        <x-form.input name="name" label="Supplier name" :value="$supplier->name" required maxlength="255" autofocus />
        <div class="row">
            <div class="col-md-6">
                <x-form.input name="email" type="email" label="Email" :value="$supplier->email" maxlength="255" help="Optional." />
            </div>
            <div class="col-md-6">
                <x-form.input name="phone" type="tel" label="Phone" :value="$supplier->phone" maxlength="50" help="Optional, e.g. +94 11 234 5678" />
            </div>
        </div>
        <x-form.textarea name="address" label="Address" :value="$supplier->address" rows="3" help="Optional." />
    </div>
    <div class="card-footer bg-white d-flex gap-2">
        <button type="submit" class="btn btn-success"><i class="bi bi-check2 me-1" aria-hidden="true"></i> {{ $supplier->exists ? 'Save changes' : 'Create supplier' }}</button>
        <a href="{{ $supplier->exists ? route('admin.suppliers.show', $supplier) : route('admin.suppliers.index') }}" class="btn btn-light border">Cancel</a>
    </div>
</div>
