<x-app-layout>
    <x-slot name="title">Add product</x-slot>
    <x-slot name="actions">
        <a href="{{ area_route('products.index') }}" class="btn btn-light border">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Back to products
        </a>
    </x-slot>

    <form method="POST" action="{{ area_route('products.store') }}" enctype="multipart/form-data" novalidate>
        @csrf
        @include('products._form')

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success"><i class="bi bi-check2 me-1" aria-hidden="true"></i> Create product</button>
            <a href="{{ area_route('products.index') }}" class="btn btn-light border">Cancel</a>
        </div>
    </form>
</x-app-layout>
