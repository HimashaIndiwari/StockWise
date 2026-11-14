<x-app-layout>
    <x-slot name="title">Add user</x-slot>
    <form method="POST" action="{{ route('admin.users.store') }}" novalidate>
        @csrf
        @include('users._form')
    </form>
</x-app-layout>
