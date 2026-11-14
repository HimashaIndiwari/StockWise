<x-app-layout>
    <x-slot name="title">Edit user</x-slot>
    <x-slot name="subheader">{{ $user->name }} · {{ $user->email }}</x-slot>
    <form method="POST" action="{{ route('admin.users.update', $user) }}" novalidate>
        @csrf
        @method('PUT')
        @include('users._form')
    </form>
</x-app-layout>
