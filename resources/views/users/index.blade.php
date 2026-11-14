<x-app-layout>
    <x-slot name="title">User Management</x-slot>
    <x-slot name="subheader">Create accounts, assign roles and activate or deactivate users.</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.users.create') }}" class="btn btn-success"><i class="bi bi-person-plus me-1" aria-hidden="true"></i> Add user</a>
    </x-slot>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-end" role="search">
                <div class="col-12 col-md-5">
                    <label for="q" class="form-label small mb-1">Search</label>
                    <input type="search" name="q" id="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Name or email">
                </div>
                <div class="col-6 col-md-2">
                    <label for="role" class="form-label small mb-1">Role</label>
                    <select name="role" id="role" class="form-select">
                        <option value="">All roles</option>
                        <option value="admin" @selected(($filters['role'] ?? '') === 'admin')>Admin</option>
                        <option value="staff" @selected(($filters['role'] ?? '') === 'staff')>Staff</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label for="status" class="form-label small mb-1">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Any status</option>
                        <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                        <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title h6 mb-0">{{ number_format($users->total()) }} {{ Str::plural('user', $users->total()) }}</h2>
        </div>
        <div class="card-body p-0">
            @if ($users->isEmpty())
                <x-empty-state icon="people" title="No users found">Try changing the filters.</x-empty-state>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col" class="d-none d-md-table-cell">Email</th>
                            <th scope="col">Role</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end d-none d-sm-table-cell">Products</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.users.show', $user) }}" class="fw-semibold">{{ $user->name }}</a>
                                    @if ($user->is(auth()->user()))<span class="small text-muted">(you)</span>@endif
                                    <div class="small text-muted d-md-none">{{ $user->email }}</div>
                                </td>
                                <td class="d-none d-md-table-cell small">{{ $user->email }}</td>
                                <td><x-role-badge :user="$user" /></td>
                                <td><x-user-status :user="$user" /></td>
                                <td class="text-end d-none d-sm-table-cell">{{ $user->created_products_count }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil" aria-hidden="true"></i><span class="visually-hidden">Edit {{ $user->name }}</span>
                                    </a>
                                    @unless ($user->is(auth()->user()))
                                        @include('users._status-toggle', ['user' => $user, 'compact' => true])
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if ($users->hasPages())
            <div class="card-footer bg-white">{{ $users->links() }}</div>
        @endif
    </div>
</x-app-layout>
