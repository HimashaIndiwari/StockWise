@props(['user'])
@if ($user->isAdmin())
    <x-status-badge type="navy" icon="shield-lock" {{ $attributes }}>Admin</x-status-badge>
@else
    <x-status-badge type="neutral" icon="person-badge" {{ $attributes }}>Staff</x-status-badge>
@endif
