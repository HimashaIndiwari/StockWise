<x-app-layout>
    <x-slot name="title">Add category</x-slot>
    <form method="POST" action="{{ route('admin.categories.store') }}" novalidate>
        @csrf
        @include('categories._form')
    </form>
</x-app-layout>
