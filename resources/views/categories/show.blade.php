<x-app-layout>
    <x-slot name="title">{{ $category->name }}</x-slot>
    <x-slot name="subheader">{{ $category->description ?: 'Category' }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Categories</a>
        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary"><i class="bi bi-pencil me-1" aria-hidden="true"></i> Edit</a>
        <x-confirm-form :action="route('admin.categories.destroy', $category)"
                        :message="'Delete category “'.$category->name.'”? This cannot be undone.'"
                        button-class="btn btn-outline-danger" />
    </x-slot>

    @include('partials.product-mini-table', ['products' => $products, 'emptyText' => 'No active products are assigned to this category.'])
</x-app-layout>
