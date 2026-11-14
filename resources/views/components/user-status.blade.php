@props(['user'])
@if ($user->isActive())
    <x-status-badge type="success" icon="check-circle" {{ $attributes }}>Active</x-status-badge>
@else
    <x-status-badge type="danger" icon="slash-circle" {{ $attributes }}>Inactive</x-status-badge>
@endif
