<x-app-layout>
    <x-slot name="title">{{ $user->name }}</x-slot>
    <x-slot name="subheader">{{ $user->email }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.users.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Users</a>
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary"><i class="bi bi-pencil me-1" aria-hidden="true"></i> Edit</a>
        @unless ($user->is(auth()->user()))
            @include('users._status-toggle', ['user' => $user])
        @endunless
    </x-slot>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h2 class="card-title h6 mb-0">Account</h2></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted fw-normal">Role</dt>
                        <dd class="col-7"><x-role-badge :user="$user" /></dd>
                        <dt class="col-5 text-muted fw-normal">Status</dt>
                        <dd class="col-7"><x-user-status :user="$user" /></dd>
                        <dt class="col-5 text-muted fw-normal">Member since</dt>
                        <dd class="col-7">{{ $user->created_at?->format('Y-m-d') ?? '—' }}</dd>
                        <dt class="col-5 text-muted fw-normal">Last updated</dt>
                        <dd class="col-7 mb-0">{{ $user->updated_at?->format('Y-m-d H:i') ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="row g-3">
                <div class="col-sm-6">
                    <x-stat-card label="Products created" :value="number_format($user->created_products_count)" icon="box-seam"
                                 :href="route('admin.products.index', ['created_by' => $user->id])" />
                </div>
                <div class="col-sm-6">
                    <x-stat-card label="Stock movements recorded" :value="number_format($user->stock_movements_count)" icon="arrow-left-right" color="success"
                                 :href="route('admin.stock-movements.index', ['user_id' => $user->id])" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
