{{-- Sortable column header that preserves current search/filter query parameters. --}}
@props(['column', 'sort', 'direction'])
@php
    $isActive = $sort === $column;
    $next = $isActive && $direction === 'asc' ? 'desc' : 'asc';
    $url = request()->fullUrlWithQuery(['sort' => $column, 'direction' => $next, 'page' => null]);
@endphp
<a href="{{ $url }}" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1"
   @if ($isActive) aria-sort="{{ $direction === 'asc' ? 'ascending' : 'descending' }}" @endif>
    {{ $slot }}
    @if ($isActive)
        <i class="bi bi-caret-{{ $direction === 'asc' ? 'up' : 'down' }}-fill" aria-hidden="true"></i>
    @else
        <i class="bi bi-chevron-expand text-muted opacity-50" aria-hidden="true"></i>
    @endif
</a>
