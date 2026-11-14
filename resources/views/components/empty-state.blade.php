@props(['icon' => 'inbox', 'title' => 'Nothing here yet'])
<div {{ $attributes->class('empty-state') }}>
    <i class="bi bi-{{ $icon }} d-block mb-2" aria-hidden="true"></i>
    <div class="fw-semibold text-body mb-1">{{ $title }}</div>
    @if (trim($slot) !== '')
        <div class="small">{{ $slot }}</div>
    @endif
    @isset($action)
        <div class="mt-3">{{ $action }}</div>
    @endisset
</div>
