<x-app-layout>
    <x-slot name="title">Edit category</x-slot>
    <x-slot name="subheader">{{ $category->name }}</x-slot>
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" novalidate>
        @csrf
        @method('PUT')
        @include('categories._form')
    </form>
</x-app-layout>
