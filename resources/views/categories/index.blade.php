<x-app-layout>
    <x-slot name="title">Categories</x-slot>
    <x-slot name="subheader">Group products into categories.</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Add category
        </a>
    </x-slot>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h2 class="card-title h6 mb-0">{{ number_format($categories->total()) }} {{ Str::plural('category', $categories->total()) }}</h2>
            <x-search-form :action="route('admin.categories.index')" :value="$search" placeholder="Search categories" />
        </div>
        <div class="card-body p-0">
            @if ($categories->isEmpty())
                <x-empty-state icon="tags" title="No categories found">
                    {{ $search ? 'No category matches your search.' : 'Create a category before adding products.' }}
                </x-empty-state>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col" class="d-none d-md-table-cell">Description</th>
                            <th scope="col" class="text-end">Products</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td><a href="{{ route('admin.categories.show', $category) }}" class="fw-semibold">{{ $category->name }}</a></td>
                                <td class="d-none d-md-table-cell text-muted small">{{ Str::limit($category->description, 80) ?: '—' }}</td>
                                <td class="text-end">{{ $category->products_count }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil" aria-hidden="true"></i><span class="visually-hidden">Edit {{ $category->name }}</span>
                                    </a>
                                    <x-confirm-form :action="route('admin.categories.destroy', $category)"
                                                    :message="'Delete category “'.$category->name.'”? This cannot be undone.'"
                                                    :label="'Delete '.$category->name" label-class="visually-hidden" />
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if ($categories->hasPages())
            <div class="card-footer bg-white">{{ $categories->links() }}</div>
        @endif
    </div>
</x-app-layout>
