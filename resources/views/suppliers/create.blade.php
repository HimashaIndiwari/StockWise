<x-app-layout>
    <x-slot name="title">Add supplier</x-slot>
    <form method="POST" action="{{ route('admin.suppliers.store') }}" novalidate>
        @csrf
        @include('suppliers._form')
    </form>
</x-app-layout>
