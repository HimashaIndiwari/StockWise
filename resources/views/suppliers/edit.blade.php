<x-app-layout>
    <x-slot name="title">Edit supplier</x-slot>
    <x-slot name="subheader">{{ $supplier->name }}</x-slot>
    <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}" novalidate>
        @csrf
        @method('PUT')
        @include('suppliers._form')
    </form>
</x-app-layout>
