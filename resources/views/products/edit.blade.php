<x-app-layout>
    <x-slot name="title">Edit product</x-slot>
    <x-slot name="subheader">{{ $product->name }} · {{ $product->sku }}</x-slot>
    <x-slot name="actions">
        <a href="{{ area_route('products.show', $product) }}" class="btn btn-light border">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Back to product
        </a>
    </x-slot>

    <form method="POST" action="{{ area_route('products.update', $product) }}" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
        @include('products._form')

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1" aria-hidden="true"></i> Save changes</button>
            <a href="{{ area_route('products.show', $product) }}" class="btn btn-light border">Cancel</a>
        </div>
    </form>
</x-app-layout>
